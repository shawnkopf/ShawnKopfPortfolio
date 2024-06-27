<?php

namespace App\Jobs;

use App\Services\KlaviyoService;
use App\Services\Netsuite\NetsuiteTransactionItemService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use App\Models\Netsuite\NetsuiteCustomer;


class CreateOrUpdateKlaviyoProfileJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(KlaviyoService $klaviyoService, NetsuiteTransactionItemService $netsuiteTransactionItemService)
    {
        try {
            $threeDaysAgo = now()->subDays(3);
            NetsuiteCustomer::where('updated_at', '>=', $threeDaysAgo)
                ->chunk(500, function ($customers) use ($klaviyoService, $netsuiteTransactionItemService) {
                    foreach ($customers as $customer) {
                        $customerProperties = $klaviyoService->mapCustomerToKlaviyoProperties($customer);

                        $mostRecentTransaction = $customer->transactions()
                            ->orderBy('tran_date', 'desc')
                            ->value('tran_date');

                        $customProperties = [
                            'last_order_date' => $mostRecentTransaction ?? "No Last Order Date",
                            'ytd_sales' => $netsuiteTransactionItemService->getYearToDateSales($customer) ?? 0,
                            'sales_rep_id' => $customer->sales_rep_id ?? "No Sales Rep Id"
                        ];

                        Log::info('Handling job to create or update Klaviyo profile.', [
                            'customerProperties' => $customerProperties,
                            'customProperties' => $customProperties,
                            'revisionDate' => now()->format('Y-m-d')
                        ]);

                        $klaviyoService->importProfilesBulk([[
                            "type" => "profile",
                            "attributes" => array_merge($customerProperties, ['properties' => $customProperties])
                        ]], $listId = null);

                        $email = $customerProperties['email'];
                        $existingCustomer = NetsuiteCustomer::where('email', $email)->first();

                        if ($existingCustomer) {
                            $profileId = $klaviyoService->getKlaviyoProfileIdByEmail($existingCustomer->email);
                            $existingProperties = $profile['attributes']['properties'] ?? [];
                            $updatedProperties = [];

                            foreach ($customProperties as $key => $value) {
                                if (!isset($existingProperties[$key])) {
                                    $updatedProperties[$key] = $value;
                                    Log::info("Adding new custom property", ['key' => $key, 'value' => $value]);
                                } else {
                                    Log::info("Custom property already exists", ['key' => $key]);
                                }
                            }

                            if (!empty($updatedProperties)) {
                                Log::info("Updating profile with new properties", ['profileId' => $profileId, 'updatedProperties' => $updatedProperties]);
                                $klaviyoService->updateProfileWithCustomProperties($profileId, $updatedProperties);
                            } else {
                                Log::info("No new properties to update for profile", ['profileId' => $profileId]);
                            }


                            if ($profileId) {
                                $subscriptionStatus = $klaviyoService->getKlaviyoProfileSubscriptionStatus($existingCustomer->email);
                                $isOptedOutInKlaviyo = $subscriptionStatus && $subscriptionStatus['email']['marketing']['can_receive_email_marketing'] === false;

                                if ($existingCustomer->global_subscription_status === 'Confirmed Opt-Out' && !$isOptedOutInKlaviyo) {
                                    Log::info('Customer opted out in Klaviyo, unsubscribing.', ['email' => $existingCustomer->email]);
                                    $klaviyoService->unsubscribeKlaviyoProfile($existingCustomer->email);
                                }
                            }
                        }
                    }
                });
        } catch (\Exception $e) {
            Log::error('Failed to process Klaviyo profile.', ['error' => $e->getMessage()]);
        }
    }
}
