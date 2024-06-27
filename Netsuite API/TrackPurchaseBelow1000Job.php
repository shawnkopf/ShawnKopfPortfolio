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
use Illuminate\Support\Facades\Log;

class TrackPurchaseBelow1000Job implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    /**
     * Create a new job instance.
     *
     * @return void
     */


    protected $klaviyoService;
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(KlaviyoService $klaviyoService)
    {
        $this->klaviyoService = $klaviyoService;
        $currentYear = now()->year;

        $transactionBelow1000 = NetsuiteTransactionItem::query()
            ->join('netsuite_customers', 'netsuite_transaction_items.customer_id', '=', 'netsuite_customers.id')
            ->whereYear('netsuite_transaction_items.created_date', $currentYear)
            ->groupBy('netsuite_transaction_items.customer_id')
            ->selectRaw('customer_id, SUM(net_amount) as total_spent')
            ->havingRaw('SUM(net_amount) < 1000')
            ->with('customer')
            ->get();

        Log::info('TrackPurchaseBelow1000Job: Found ' . $transactionBelow1000->count() . ' customers.');

        $revisionDate = config('api_revision_date');


        foreach ($transactionBelow1000 as $record) {
            $customer = $record->customer;
            if ($customer) {
                $eventProperties = [
                    'Total Amount' => $record->total_spent,
                    'Year' => $currentYear,
                ];
                $customerProperties = $klaviyoService->mapCustomerToKlaviyoProperties($customer, $eventProperties);

                $klaviyoService->createOrUpdateProfile($customerProperties, $revisionDate);

                $eventName = 'Total Purchase Below $1000 in ' . $currentYear;

                $klaviyoService->trackEvent($eventName, $customerProperties, $eventProperties);
            }
        }
    }
}
