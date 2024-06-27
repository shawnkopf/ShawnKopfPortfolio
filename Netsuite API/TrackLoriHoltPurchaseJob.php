<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\KlaviyoService;
use App\Models\Netsuite\NetsuiteTransactionItem;

class TrackLoriHoltPurchaseJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */


    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $klaviyoService = app(KlaviyoService::class);

        $loriHoltPurchases = NetsuiteTransactionItem::where('item_sku', 'like', '%LoriHoltSKU123%') // replace with pattern identifier for lori holt sku
            ->get();
        // if using item_id:
        // $loriHoltPurchases = NetsuiteTransactionItem::whereIn('item_id', [/* array of Lori Holt product IDs */])
        //     ->get();

        $revisionDate = config('api_revision_date');

        foreach ($loriHoltPurchases as $purchase) {
            $customer = $purchase->customer;
            $customerProperties = $klaviyoService->mapCustomerToKlaviyoProperties($customer);
            $klaviyoService->createOrUpdateProfile($customerProperties, $revisionDate);

            $eventName = 'Purchased Lori Holt Item';
            $eventProperties = [
                'ItemSKU' => $purchase->item_sku,
                // Include other relevant details
            ];
            $klaviyoService->trackEvent($eventName, $customerProperties, $eventProperties);
        }
    }
}
