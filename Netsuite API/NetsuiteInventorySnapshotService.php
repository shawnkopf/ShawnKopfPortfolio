<?php

namespace App\Services\Netsuite;

use App\Models\Netsuite\NetsuiteInventorySnapshot;
use Illuminate\Support\Facades\DB;

class NetsuiteInventorySnapshotService
{
    public function __construct(public NetsuiteService $netsuiteService)
    {
    }

    public function getInventoryLevels($url, $q)
    {
        return $this->netsuiteService->makeNsQuery($q, $url);
    }

    public function saveInventoryLevels($inventoryLevels)
    {
        $today = date('Y-m-d');
        $insertData = [];

        foreach ($inventoryLevels as $level) {
            $level = (object) $level;
            $existingSnapshot = NetsuiteInventorySnapshot::where('ns_item_id', $level->item)
                ->orderBy('snapshot_date', 'desc')
                ->first();

            $quantityAvailable = $level->quantityavailable ?? 0;
            $quantityOnHand = $level->quantityonhand ?? 0;
            $quantityPicked = $level->quantitypicked ?? 0;

            if ($existingSnapshot) {
                $quantityAvailable += $existingSnapshot->quantity_available;
                $quantityOnHand += $existingSnapshot->quantity_on_hand;
                $quantityPicked += $existingSnapshot->quantity_picked;
            }

            $insertData[] = [
                'snapshot_date' => $today,
                'ns_item_id' => $level->item,
                'quantity_available' => $quantityAvailable,
                'quantity_on_hand' => $quantityOnHand,
                'quantity_picked' => $quantityPicked
            ];
        }

        DB::transaction(function () use ($insertData) {
            foreach ($insertData as $data) {
                NetsuiteInventorySnapshot::updateOrCreate(
                    [
                        'snapshot_date' => $data['snapshot_date'],
                        'ns_item_id' => $data['ns_item_id'],
                    ],
                    [
                        'quantity_available' => $data['quantity_available'],
                        'quantity_on_hand' => $data['quantity_on_hand'],
                        'quantity_picked' => $data['quantity_picked'],
                    ]
                );
            }
        });
        return true;
    }
}
