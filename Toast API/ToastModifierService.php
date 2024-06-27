<?php

namespace App\Services\Toast;


use App\Models\Toast\ToastModifier;
use Illuminate\Support\Facades\Log;

/**
 * Class ToastModifierService
 * @package App\Services
 */
class ToastModifierService
{
    public function saveModifiers($modifiers)
    {
        $upsertBody = [];
        foreach ($modifiers['modifiers'] as $modifier) {
            $modifier = (object) $modifier;

            $upsertBody[] = [
                'guid' => $modifier->guid,
                //                'entity_type' => $modifier['entityType'],
                //                'pre_discount_price' => $modifier['preDiscountPrice'],
                //                'option_group_guid' => $modifier['optionGroup']->guid ?? null,
                //                'option_group_entity_type' => $modifier['optionGroup']->entityType ?? null,
                'display_name' => $modifier->displayName ?? null,
                //                'applied_discounts' => json_encode($modifier['appliedDiscounts']),
                // 'sales_category' => $modifier->salesCategory ?? null,
                'selection_type' => $modifier->selectionType ?? null,
                'selection_guid' => $modifier->selection_guid ?? null,
                //                'voided' => $modifier['voided'],
                //                'price' => $modifier['price'],
                'item_guid' => $modifier->item->guid ?? null,


                //                'item_entity_type' => $modifier['item']->entityType ?? null,
                'quantity' => $modifier->quantity,
                //                'unit_of_measure' => $modifier['unitOfMeasure'],
                //                'tax' => $modifier['tax']
            ];
        }

        try {
            ToastModifier::upsert(
                $upsertBody,
                ['guid'],
                [
                    //                    'entity_type',
                    //                    'pre_discount_price',
                    //                    'option_group_guid',
                    //                    'option_group_entity_type',
                    'display_name',
                    //                    'applied_discounts',
                    // 'sales_category',
                    'selection_type',
                    'selection_guid',
                    //                    'voided',
                    //                    'price',
                    'item_guid',
                    //                    'item_entity_type',
                    'quantity',
                    //                    'unit_of_measure',
                    //                    'tax'
                ]
            );
        } catch (\Throwable $e) {
        }
    }
}
