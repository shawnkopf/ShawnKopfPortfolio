<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\KlaviyoService;
use App\Models\Netsuite\NetsuiteCustomer;
use App\Services\Netsuite\NetsuiteTransactionItemService;
use Illuminate\Support\Facades\Log;
use Exception;

class UpdateKlaviyoProfileLastOrderDateJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(KlaviyoService $klaviyoService, NetsuiteTransactionItemService $netsuiteTransactionItemService)
    {
        try {
            NetsuiteCustomer::chunk(500, function ($customers) use ($klaviyoService, $netsuiteTransactionItemService) {
                $profiles = [];

                foreach ($customers as $netsuiteCustomer) {
                    $mostRecentTransaction = $netsuiteCustomer->transactions()->min('tran_date');
                    $ytdSales = $netsuiteTransactionItemService->getYearToDateSales($netsuiteCustomer->ns_customer_id);

                    if ($mostRecentTransaction) {
                        $profiles[] = [
                            'type' => 'profile',
                            'attributes' => [
                                'email' => $netsuiteCustomer->email,
                                'properties' => [
                                    'last_order_date' => $mostRecentTransaction,
                                    'ytd_sales' => $ytdSales,
                                    'sales_rep_id' => $netsuiteCustomer->sales_rep_id
                                ]
                            ]
                        ];
                    }

                    // If profiles array reaches 10,000, send bulk import request
                    if (count($profiles) >= 10000) {
                        $klaviyoService->importProfilesBulk($profiles);
                        Log::info('Bulk profiles imported successfully to Klaviyo.');
                        $profiles = [];
                    }
                }

                if (!empty($profiles)) {
                    $klaviyoService->importProfilesBulk($profiles);
                    Log::info('Bulk profiles imported successfully to Klaviyo.');
                }
            });
        } catch (Exception $e) {
            Log::error('Failed to update Klaviyo profiles in bulk.', ['error' => $e->getMessage()]);
        }
    }
}
