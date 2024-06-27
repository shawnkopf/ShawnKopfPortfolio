<?php

namespace App\Services\Netsuite;

use App\Models\Netsuite\NetsuiteItem;
use Illuminate\Support\Facades\DB;

class NetsuiteItemService
{
    public function __construct(public NetsuiteService $netsuiteService)
    {
    }
    public function getMaxItemId()
    {
        $url ='https://4582045.suitetalk.api.netsuite.com/services/rest/query/v1/suiteql';
        $q = [
            "q" => "select max(id) from Item"
        ];

        $response = $this->netsuiteService->makeNsQuery($q, $url);

        return $response['items'][0]['expr1'];
    }

    public function getItems($url, $q)
    {
        return $this->netsuiteService->makeNsQuery($q, $url);
    }

    public function saveItems($items)
    {
        $upsertBody = [];
        $upsertFields = [
            'royalty_rate_artist_1',
            'anticipated_release_date',
            'country_code',
            'lifecycle',
            'packaging',
            'artist_1',
            'artist_2',
            'rbd_collection',
            'rbd_release',
            'discontinued_date',
            'last_call_date',
            'item_per_yd',
            'pcs',
            'display_name',
            'is_inactive',
            'rbd_item_id',
            'last_purchase_price',
            'rbd_moq',
            'purchase_description',
            'store_detailed_description',
            'search_keywords',
            'unit_type',
            'upc_code',
            'total_value',
            'vendor_moq',
            'fiber_content',
            'width',
            'washing_instructions',
            'color',
            'rbd_print_type',
            'vendor',
            'cost',
            'category',
            'theme',
            'pattern',
            'is_online',
            'is_christmas',
            'is_closeout',
            'closeout_date',
            'is_fall',
            'is_halloween',
            'included_in_casepack',
            'kit_pct_for_artist',
            'license_applicable',
            'is_patriotic',
            'rbd_preorder_only',
            'rbd_printing_pattern',
            'reorderable',
            'royalty_rate_artist_2',
            'royalty_amount_per_yard',
            'is_spring',
            'is_valentines',
            'ns_item_id',
            'class',
            're_release_date'
        ];

        foreach ($items as $item) {
            $item = (object) $item;
            $upsertBody[] = [
                'royalty_rate_artist_1' => $item->custitem15 ?? null,
                'anticipated_release_date' => isset($item->custitem29) ? date('Y-m-d', strtotime($item->custitem29)) : null,
                'country_code' => $item->country ?? null,
                'lifecycle' => $item->lifecycle ?? null,
                'packaging' => $item->packaging ?? null,
                'artist_1' => $item->artist1 ?? null,
                'artist_2' => $item->artist2 ?? null,
                'rbd_collection' => $item->rbdcollection ?? null,
                'rbd_release' => $item->rbdrelease ?? null,
                'discontinued_date' => isset($item->custitemdiscontinued) ? date('Y-m-d', strtotime($item->custitemdiscontinued)) : null,
                'last_call_date' => isset($item->custitemlast_call_date) ? date('Y-m-d', strtotime($item->custitemlast_call_date)) : null,
                'item_per_yd' => $item->custitemper_yd ?? null,
                'pcs' => $item->custitempcs ?? null,
                'display_name' => $item->displayname ?? null,
                'is_inactive' => ($item->isinactive ?? null) === "T",
                'rbd_item_id' => $item->itemid ?? null,
                'last_purchase_price' => $item->lastpurchaseprice ?? null,
                'rbd_moq' => $item->custitem34 ?? null,
                'purchase_description' => $item->purchasedescription ?? null,
                'store_detailed_description' => $item->storedetaileddescription ?? null,
                'search_keywords' => $item->searchkeywords ?? null,
                'unit_type' => $item->unittype ?? null,
                'upc_code' => $item->upccode ?? null,
                'total_value' => $item->totalvalue ?? null,
                'vendor_moq' => $item->custitem3 ?? null,
                'fiber_content' => $item->fibercontent ?? null,
                'width' => $item->width ?? null,
                'washing_instructions' => $item->washinginstructions ?? null,
                'color' => $item->rbd_color ?? null,
                'rbd_print_type' => $item->printtype ?? null,
                'vendor' => $item->vendor ?? null,
                'cost' => $item->cost ?? null,
                'category' => $item->category ?? null,
                'theme' => $item->theme ?? null,
                'pattern' => $item->pattern ?? null,
                'is_online' => ($item->isonline ?? null) === "T",
                'is_christmas' => ($item->custitem50 ?? null) === "T",
                'is_closeout' => ($item->custitemcloseout ?? null)  === "T",
                'closeout_date' => isset($item->custitem28) ? date('Y-m-d', strtotime($item->custitem28)) : null,
                'is_fall' => ($item->custitem52 ?? null) === "T",
                'is_halloween' => ($item->custitem49 ?? null) === "T",
                'included_in_casepack' => ($item->custitem61 ?? null) === "T",
                'kit_pct_for_artist' => $item->custitem19 ?? null,
                'license_applicable' => ($item->custitem22 ?? null) === "T",
                'is_patriotic' => ($item->custitem53 ?? null) === "T",
                'rbd_preorder_only' => ($item->custitemrbd_preorder_only ?? null) === "T",
                'rbd_printing_pattern' => ($item->custitem70 ?? null) === "T",
                'reorderable' => $item->reorderable ?? null,
                'royalty_rate_artist_2' => $item->custitem23 ?? null,
                'royalty_amount_per_yard' => $item->custitem24 ?? null,
                'is_spring' => ($item->custitem54 ?? null) === "T",
                'is_valentines' => ($item->custitem51 ?? null) === "T",
                'ns_item_id' => ($item->id),
                'class' => $item->class ?? null,
                're_release_date' => isset($item->custitem73) ? date('Y-m-d', strtotime($item->custitem73)) : null,
            ];
        }

        DB::transaction(function () use($upsertBody, $upsertFields) {
            NetsuiteItem::upsert($upsertBody, ['ns_item_id'], $upsertFields);
        }, 5);
        return true;
    }
}
