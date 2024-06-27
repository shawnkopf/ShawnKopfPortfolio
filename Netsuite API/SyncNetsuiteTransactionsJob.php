<?php

namespace App\Jobs\Netsuite;

use App\Services\Netsuite\NetsuiteService;
use App\Services\Netsuite\NetsuiteTransactionService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SyncNetsuiteTransactionsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(NetsuiteTransactionService $netsuiteTransactionService)
    {
        $date = now();

        $lookBackDate = date('Y-m-d H:i:s', strtotime($date . ' - 10 days'));
        $q = [
            'q' => "SELECT actualshipdate,lastmodifieddate,type,shippingaddress,custbody31 as containernumber,custbody21 as vendorshipdate,createddate,trandate,BUILTIN.DF(createdby) as createdBy,daysopen,BUILTIN.DF(employee) as employeeName,employee,BUILTIN.DF(entity) as entityName,entity,id,linkedtrackingnumberlist,shipcarrier,shipdate,duedate as expectreceiptdate,custbodyetapo as etatocustomers,source,BUILTIN.DF(terms) as terms,tranid,BUILTIN.DF(status) as status,custbody23,BUILTIN.DF(custbodyorder_source) as order_source from transaction where type in ('SalesOrd', 'CustInvc', 'PurchOrd', 'ItemRcpt', 'CashSale') and lastmodifieddate > TO_TIMESTAMP( '$lookBackDate', 'YYYY-MM-DD HH24:MI:SSxFF' )"
        ];
        $url = 'https://4582045.suitetalk.api.netsuite.com/services/rest/query/v1/suiteql?limit=1000';
        do {
            echo $url . PHP_EOL;
            $transactions = $netsuiteTransactionService->getTransactions($url, $q);
            $netsuiteTransactionService->saveTransactions($transactions['items']);
            $links = $transactions['links'];
            $url = false;
            foreach ($links as $link) {
                if ($link['rel'] === 'next') {
                    $url = $link['href'];
                }
            }
        } while ($url);
    }
}
