<?php

namespace App\Jobs\Netsuite;

use App\Models\Netsuite\NetsuiteTransactionItem;
use App\Services\Netsuite\NetsuiteService;
use App\Services\Netsuite\NetsuiteTransactionItemService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\RateLimitedMiddleware\RateLimited;

class SyncNetsuiteTransactionItemsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle($date=null)
    {
        $netsuiteTransactionItemService = new NetsuiteTransactionItemService(new NetsuiteService());
        if(is_null($date)) {
            $date = now();
        }

        $lookBackDate = date('Y-m-d H:i:s', strtotime($date. ' - 4 days'));
        $q = [
            "q" => "SELECT iscogs,createdfrom,dropship,BUILTIN.DF(entity) as entityName,entity,id,isclosed,actualshipdate,isfullyshipped,BUILTIN.DF(item) as sku,item,quantity,quantitybackordered,quantityshiprecv,rate,specialorder,transaction,uniquekey,custcol12,BUILTIN.DF(price) as pricelevel,location,BUILTIN.DF(location) as locationname,expenseaccount,BUILTIN.DF(expenseaccount) as expenseaccountname,itemtype,foreignamount,netamount,ratepercent, from transactionLine where itemtype in ('Discount', 'EndGroup', 'GiftCert', 'Group', 'InvtPart', 'NonInvtPart', 'OthCharge', 'Payment', 'ShipItem', 'TaxGroup', 'TaxItem') and linelastmodifieddate > TO_TIMESTAMP( '$lookBackDate', 'YYYY-MM-DD HH24:MI:SSxFF' )"
        ];
        echo($q['q']);
        $url = 'https://4582045.suitetalk.api.netsuite.com/services/rest/query/v1/suiteql?limit=1000';
        do {
            echo $url . PHP_EOL;
            $transactionItems = $netsuiteTransactionItemService->getTransactionItems($url, $q);
            $netsuiteTransactionItemService->saveTransactionItems($transactionItems['items']);
            $netsuiteTransactionItemService->connectTransactionItemCost($transactionItems['items']);
            $links = $transactionItems['links'];
            $url = false;
            foreach($links as $link) {
                if($link['rel'] === 'next') {
                    $url = $link['href'];
                }
            }
        } while ($url);

    }
}
