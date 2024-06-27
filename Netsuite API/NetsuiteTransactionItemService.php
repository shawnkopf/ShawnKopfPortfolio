<?php

namespace App\Services\Netsuite;


use App\Models\Netsuite\NetsuiteTransactionItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class NetsuiteTransactionItemService
{
    public function __construct(public NetsuiteService $netsuiteService)
    {
    }

    public function getMinTransactionLineId()
    {
        $max = NetsuiteTransactionItem::max('unique_key');
        if (!$max) {
            $max = 0;
        }
        return $max;
    }

    public function getMaxTransactionLineId()
    {
        $url = 'https://4582045.suitetalk.api.netsuite.com/services/rest/query/v1/suiteql';
        $q = [
            'q' => "SELECT max(uniquekey) from transactionLine"
        ];

        $response = $this->netsuiteService->makeNsQuery($q, $url);

        return $response['items'][0]['expr1'];
    }

    public function getTransactionItems($url, $q)
    {
        return $this->netsuiteService->makeNsQuery($q, $url);
    }

    public function saveTransactionItems($transactionItems)
    {
        $upsertBody = [];
        $upsertFields = [
            'is_cogs',
            'created_from',
            'is_dropship',
            'entity_name',
            'entity',
            'ns_transaction_item_id',
            'is_closed',
            'actual_ship_date',
            'is_fully_shipped',
            'item_sku',
            'item_id',
            'quantity',
            'quantity_backordered',
            'quantity_ship_recv',
            'rate',
            'special_order',
            'ns_transaction_id',
            'unique_key',
            'rbd_trend_exclude',
            'price_level',
            'location_id',
            'location_name',
            'expense_account_id',
            'expense_account_name',
            'item_type',
            'foreign_amount',
            'net_amount',
            'rate_percent',
            'cost',
            'combined_key',
        ];

        foreach ($transactionItems as $item) {
            $item = (object) $item;
            $upsertBody[] = [
                'is_cogs' => ($item->iscogs ?? null) === "T",
                'created_from' => $item->createdfrom ?? null,
                'is_dropship' => ($item->dropship ?? null) === "T",
                'entity_name' => $item->entityname ?? null,
                'entity' => $item->entity ?? null,
                'ns_transaction_item_id' => $item->id ?? uniqid(),
                'is_closed' => ($item->isclosed ?? null) === "T",
                'actual_ship_date' => date('Y-m-d', strtotime($item->actualshipdate ?? null)),
                'is_fully_shipped' => ($item->isfullyshipped ?? null) === "T",
                'item_sku' => $item->sku ?? null,
                'item_id' => $item->item ?? null,
                'quantity' => abs($item->quantity ?? 0),
                'quantity_backordered' => $item->quantitybackordered ?? null,
                'quantity_ship_recv' => $item->quantityshiprecv ?? null,
                'rate' => $item->rate ?? null,
                'special_order' => ($item->specialorder ?? null) === "T",
                'ns_transaction_id' => $item->transaction ?? null,
                'unique_key' => $item->uniquekey ?? null,
                'rbd_trend_exclude' => ($item->custcol12 ?? null) === "T",
                'price_level' => $item->pricelevel ?? null,
                'location_id' => $item->location ?? null,
                'location_name' => $item->locationname ?? null,
                'expense_account_id' => $item->expenseaccount ?? null,
                'expense_account_name' => $item->expenseaccountname ?? null,
                'item_type' => $item->itemtype ?? null,
                'foreign_amount' => abs($item->foreignamount ?? 0),
                'net_amount' => abs($item->netamount ?? 0),
                'rate_percent' => $item->ratepercent ?? null,
                'combined_key' => ($item->item ?? rand()) . $item->transaction,
            ];
        }

        DB::transaction(function () use ($upsertBody, $upsertFields) {
            NetsuiteTransactionItem::upsert($upsertBody, ['unique_key'], $upsertFields);
        }, 5);
        return true;
    }

    public function connectTransactionItemCost($items)
    {
        $upsertBody = [];
        $upsertFields = [
            'cost'
        ];

        foreach ($items as $item) {
            $item = (object) $item;
            if (($item->iscogs ?? null) == "T" && ($item->rate ?? false)) {
                $key = $item->item . ($item->createdfrom ?? rand());
                $upsertBody[] = [
                    'combined_key' => $key,
                    'cost' => $item->rate,
                    'unique_key' => $key,
                    'ns_transaction_item_id' => $item->id
                ];
            }
        }
        NetsuiteTransactionItem::upsert($upsertBody, ['unique_key'], $upsertFields);
    }

    public function getYearToDateSales($nsCustomerId)
    {

        $startOfYear = now()->startOfYear()->format('Y-m-d');
        $today = now()->format('Y-m-d');

        $transactions = NetsuiteTransactionItem::join('netsuite_transactions', 'netsuite_transactions.ns_transaction_id', '=', 'netsuite_transaction_items.ns_transaction_id')
            ->join('netsuite_customers', 'netsuite_transactions.entity_id', '=', 'netsuite_customers.ns_customer_id')
            ->where('netsuite_customers.ns_customer_id', $nsCustomerId)
            ->whereIn('netsuite_transactions.type', ['CashSale', 'CustInvc'])
            ->whereBetween('netsuite_transactions.tran_date', [$startOfYear, $today])
            ->select('netsuite_transaction_items.net_amount')
            ->get();

        if ($transactions->isEmpty()) {
            Log::info('No transactions found for customer.', ['ns_customer_id' => $nsCustomerId]);
        }

        $totalNetAmount = $transactions->sum('net_amount');
        Log::info('YTD Sales Calculated.', [
            'ns_customer_id' => $nsCustomerId,
            'start_of_year' => $startOfYear,
            'today' => $today,
            'transaction_count' => $transactions->count(),
            'total_net_amount' => $totalNetAmount
        ]);
        return $totalNetAmount;
    }
}
