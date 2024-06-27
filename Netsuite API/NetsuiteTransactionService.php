<?php

namespace App\Services\Netsuite;


use App\Models\Netsuite\NetsuiteTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class NetsuiteTransactionService
{
    public function __construct(public NetsuiteService $netsuiteService, public NetsuiteAddressService $netsuiteAddressService)
    {
    }
    public function getNewestTransactionId()
    {
        return NetsuiteTransaction::max('ns_transaction_id');
    }

    public function getMaxTransactionId()
    {
        $url = 'https://4582045.suitetalk.api.netsuite.com/services/rest/query/v1/suiteql';
        $q = [
            'q' => "SELECT max(id) from transaction"
        ];

        $response = $this->netsuiteService->makeNsQuery($q, $url);

        return $response['items'][0]['expr1'];
    }

    public function getTransactions($url, $q)
    {
        return $this->netsuiteService->makeNsQuery($q, $url);
    }

    public function saveTransactions($transactions)
    {
        $upsertBody = [];
        $upsertFields = [
            'actual_ship_date',
            'vendor_ship_date',
            'created_by',
            'created_date',
            'container_number',
            'days_open',
            'employee_name',
            'employee_id',
            'entity_name',
            'entity_id',
            'ns_transaction_id',
            'linked_tracking_number_list',
            'ship_carrier',
            'ship_date',
            'expect_receipt_date',
            'eta_to_customers',
            'source',
            'terms',
            'tran_date',
            'tran_id',
            'status',
            'order_description',
            'order_source',
            'type',
            'location_id',
        ];

        foreach ($transactions as $order) {
            $order = (object) $order;
            $upsertBody[] = [
                'actual_ship_date' => date('Y-m-d', strtotime($order->actualshipdate ?? null)),
                'vendor_ship_date' => date('Y-m-d', strtotime($order->vendorshipdate ?? null)),
                'created_date' => date('Y-m-d', strtotime($order->createddate ?? null)),
                'container_number' => $order->containernumber ?? null,
                'created_by' => $order->createdby ?? null,
                'days_open' => $order->daysopen ?? null,
                'employee_name' => $order->employeename ?? null,
                'employee_id' => $order->employee ?? null,
                'entity_name' => $order->entityname ?? null,
                'entity_id' => $order->entity ?? null,
                'ns_transaction_id' => $order->id ?? null,
                'linked_tracking_number_list' => $order->linkedtrackingnumberlist ?? null,
                'ship_carrier' => $order->shipcarrier ?? null,
                'ship_date' => date('Y-m-d', strtotime($order->shipdate ?? null)),
                'expect_receipt_date' => date('Y-m-d', strtotime($order->expectreceiptdate ?? null)),
                'eta_to_customers' => date('Y-m-d', strtotime($order->etatocustomers ?? null)),
                'source' => $order->source ?? null,
                'terms' => $order->terms ?? null,
                'tran_date' => date('Y-m-d', strtotime($order->trandate ?? null)),
                'tran_id' => $order->tranid ?? null,
                'status' => $order->status ?? null,
                'order_source' => $order->custbodyorder_source ?? null,
                'order_description' => $order->custbody23 ?? null,
                'type' => $order->type ?? null,
                'location_id' => $order->shippingaddress ?? null,
            ];
        }

        DB::transaction(function () use ($upsertBody, $upsertFields) {
            NetsuiteTransaction::upsert($upsertBody, ['ns_transaction_id'], $upsertFields);
        }, 5);
        return true;
    }
}
