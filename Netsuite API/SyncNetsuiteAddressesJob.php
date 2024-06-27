<?php

namespace App\Jobs\Netsuite;

use App\Services\Netsuite\NetsuiteAddressService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SyncNetsuiteAddressesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(NetsuiteAddressService $netsuiteAddressService)
    {
        $i = 0;
        do {
            $max = $i + 75000;
            $min = $i;
            $url = 'https://4582045.suitetalk.api.netsuite.com/services/rest/query/v1/suiteql?limit=1000';
            $q = [
                'q' => "SELECT
                        addr.nKey AS address_id,
                        addr.addressee AS company_name,
                        addr.addr1 AS address1,
                        addr.addr2 AS address2,
                        addr.addr3 AS address3,
                        addr.city AS city,
                        addr.country AS country,
                        addr.state AS state,
                        addr.zip AS zip
                    FROM
                        EntityAddress AS addr where nKey between $min and $max"
            ];
            do {
                echo $url . PHP_EOL;
                $addresses = $netsuiteAddressService->getAddresses($url, $q);
                $netsuiteAddressService->saveAddressToDatabase($addresses['items']);
                $links = $addresses['links'];
                $url = false;
                foreach ($links as $link) {
                    if ($link['rel'] === 'next') {
                        $url = $link['href'];
                    }
                }
            } while ($url);
            $i = $max;
        } while ($i < 3000000);
    }
}

