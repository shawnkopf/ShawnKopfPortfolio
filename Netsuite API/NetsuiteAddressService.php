<?php

namespace App\Services\Netsuite;

use App\Models\NetsuiteShippingAddress;
use App\Services\Netsuite\NetsuiteService;

class NetsuiteAddressService
{
    public function __construct(public NetsuiteService $netsuiteService)
    {
    }

    public function getAddresses($url, $q)
    {

        return $this->netsuiteService->makeNsQuery($q, $url);
    }

    public function saveAddressToDatabase($addresses)
    {
        try {
            foreach ($addresses as $address) {
                $data = [
                    'address_id' => $address['address_id'],
                    'company_name' => $address['company_name'] ?? null,
                    'address1' => $address['addr1'] ?? null,
                    'address2' => $address['addr2'] ?? null,
                    'address3' => $address['addr3'] ?? null,
                    'city' => $address['city'] ?? null,
                    'state' => $address['state'] ?? null,
                    'zip' => $address['zip'] ?? null,
                    'country' => $address['country'] ?? null
                ];

                NetsuiteShippingAddress::updateOrCreate(
                    ['address_id' => $data['address_id']],
                    $data
                );
            }
        } catch (\Exception $e) {
            echo "Error syncing address to database: " . $e->getMessage();
        }
    }
}
