<?php

namespace App\Services;

use App\Models\OrderItem;
use Illuminate\Support\Facades\Log;

class OrderItemService
{
    protected QuiltService $quiltService;

    public function __construct(QuiltService $quiltService)
    {
        $this->quiltService = $quiltService;
    }

    public function consumeOrderWebhook($requestBody)
    {
        Log::info('response', ['Response' => $requestBody]);
        $items = $requestBody['line_items'];
        foreach ($items as $item) {
            OrderItem::updateOrCreate(
                [
                    'shopify_order_item_id' => $item['id']
                ],
                [
                    'shopify_order_id' => $requestBody['id'],
                    'order_date' => $requestBody['created_at'],
                    'variant_id' => $item['variant_id'],
                    'title' => $item['title'],
                    'quantity' => $item['quantity'],
                    'sku' => $item['sku'] ?? "none",
                    'variant_title' => $item['variant_title'],
                    'vendor' => $item['vendor'],
                    'fulfillment_service' => $item['fulfillment_service'],
                    'product_id' => $item['product_id'],
                    'gift_card' => $item['gift_card'],
                    'name' => $item['name'],
                    'properties' => json_encode($item['properties']),
                    'grams' => $item['grams'],
                    'price' => $item['price'] * 100,
                    'total_discount' => $item['total_discount'] * 100,
                    'fulfillment_status' => $item['fulfillment_status'],
                ]
            );
        }

    }
}
