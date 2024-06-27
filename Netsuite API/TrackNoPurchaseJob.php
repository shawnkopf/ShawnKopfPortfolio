<?php

namespace App\Jobs\Netsuite;


use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\KlaviyoService;
use App\Models\Netsuite\NetsuiteCustomer;
use App\Models\Netsuite\NetsuiteTransactionItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;



class TrackNoPurchaseJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $klaviyoService;

    public function handle(KlaviyoService $klaviyoService)
    {
        $this->klaviyoService = $klaviyoService;
        $this->trackDaysNoPurchase(90);
        $this->trackDaysNoPurchase(180);
    }

    public function trackDaysNoPurchase($days)
    {

        $daysAgo = Carbon::now()->subDays($days);

        $customersWithNoRecentPurchase = NetsuiteCustomer::whereDoesntHave('transactions', function ($query) use ($daysAgo) {
            $query->where('created_date', '>', $daysAgo);
        })->get();
        Log::info('TrackNoPurchaseJob: ' . $customersWithNoRecentPurchase);
        $revisionDate = config('api_revision_date');;

        $eventName = "No purchase in $days Days";

        foreach ($customersWithNoRecentPurchase as $customer) {
            $eventProperties = [
                'Inactivity Period' => "$days days",
                'Last Purchase' => "No purchase in $days Days",
            ];
            $customerProperties = $this->klaviyoService->mapCustomerToKlaviyoProperties($customer, $eventProperties);

            $this->klaviyoService->createOrUpdateProfile($customerProperties, $revisionDate);

            $this->klaviyoService->trackEvent($eventName, $customerProperties, $eventProperties);
        }
    }
}
