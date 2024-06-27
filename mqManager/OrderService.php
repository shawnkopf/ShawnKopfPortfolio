<?php

namespace App\Services;

use App\Models\Order;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

class OrderService
{
    private Client $client;
    private string $url;

    public function __construct()
    {
        $this->client = new Client(["headers" => [
            'X-Shopify-Access-Token' => config('shopify.shopify_admin_access_token'),
            'Content-Type' => 'application/json'
        ]]);
        $this->url = config('shopify.shopify_store_url');
    }

    public function consumeOrderWebhook($requestBody)
    {
        Log::info('response', ['Response' => $requestBody]);
        $customer = $requestBody['customer'] ?? null;
        $customerId = $customer['id'] ?? 0;
        $billingAddress = $requestBody['billing_address'] ?? [];
        $phoneNumber = $billingAddress['phone'] ?? null;
        return Order::updateOrCreate(
            [
                'shopify_id' => $requestBody['id']
            ],
            [
                'email' => $requestBody['email'],
                'phone_number' => $phoneNumber,
                'customer_id' => $customerId,
                'first_name' => $customer['first_name'] ?? null,
                'last_name' => $customer['last_name'] ?? null,
                'shopify_created_at' => $requestBody['created_at'],
                'shopify_updated_at' => $requestBody['updated_at'],
                'shopify_closed_at' => $requestBody['closed_at'] ?? null,
                'number' => $requestBody['number'],
                'note' => $requestBody['note'],
                'test' => $requestBody['test'],
                'total_price' => $requestBody['total_price'] * 100,
                'subtotal_price' => $requestBody['subtotal_price'] * 100,
                'total_weight' => $requestBody['total_weight'],
                'total_tax' => $requestBody['total_tax'] * 100,
                'taxes_included' => $requestBody['taxes_included'],
                'financial_status' => $requestBody['financial_status'],
                'confirmed' => $requestBody['confirmed'],
                'total_discounts' => $requestBody['total_discounts'] * 100,
                'total_line_items_price' => $requestBody['total_line_items_price'] * 100,
                'name' => $requestBody['name'],
                'cancelled_at' => $requestBody['cancelled_at'],
                'cancel_reason' => $requestBody['cancel_reason'],
                'source_name' => $requestBody['source_name'],
                'fulfillment_status' => $requestBody['fulfillment_status'],
                'tags' => $requestBody['tags']
            ]
        );
    }

    public function setNewOrderMetafields(Order $order): void
    {
        $body = [
            "order" => [
                "id" => $order->shopify_id,
                "metafields" => [
                    ["key" => "order_placed_date", "value" => $order->shopify_created_at, "type" => "single_line_text_field", "namespace" => "Quilt_Updates"],
                    ["key" => "order_placed_img_url", "value" => "null", "type" => "single_line_text_field", "namespace" => "Quilt_Updates"],
                    ["key" => "first_name", "value" => "null", "type" => "single_line_text_field", "namespace" => "Quilt_Updates"],
                    ["key" => "last_name", "value" => "null", "type" => "single_line_text_field", "namespace" => "Quilt_Updates"],
                    ["key" => "quilt_received_date", "value" => "null", "type" => "single_line_text_field", "namespace" => "Quilt_Updates"],
                    ["key" => "quilt_received_img_url", "value" => "null", "type" => "single_line_text_field", "namespace" => "Quilt_Updates"],
                    ["key" => "quilt_staged_date", "value" => "null", "type" => "single_line_text_field", "namespace" => "Quilt_Updates"],
                    ["key" => "quilt_staged_img_url", "value" => "null", "type" => "single_line_text_field", "namespace" => "Quilt_Updates"],
                    ["key" => "quilt_on_machine_date", "value" => "null", "type" => "single_line_text_field", "namespace" => "Quilt_Updates"],
                    ["key" => "quilt_on_machine_img_url", "value" => "null", "type" => "single_line_text_field", "namespace" => "Quilt_Updates"],
                    ["key" => "trimming_date", "value" => "null", "type" => "single_line_text_field", "namespace" => "Quilt_Updates"],
                    ["key" => "trimming_img_url", "value" => "null", "type" => "single_line_text_field", "namespace" => "Quilt_Updates"],
                    ["key" => "binding_date", "value" => "null", "type" => "single_line_text_field", "namespace" => "Quilt_Updates"],
                    ["key" => "binding_img_url", "value" => "null", "type" => "single_line_text_field", "namespace" => "Quilt_Updates"],
                    ["key" => "packing_date", "value" => "null", "type" => "single_line_text_field", "namespace" => "Quilt_Updates"],
                    ["key" => "packing_img_url", "value" => "null", "type" => "single_line_text_field", "namespace" => "Quilt_Updates"],
                    ["key" => "shipped_date", "value" => "null", "type" => "single_line_text_field", "namespace" => "Quilt_Updates"],
                    ["key" => "shipped_img_url", "value" => "null", "type" => "single_line_text_field", "namespace" => "Quilt_Updates"],
                    ["key" => "arrived_date", "value" => "null", "type" => "single_line_text_field", "namespace" => "Quilt_Updates"],
                    ["key" => "customer_service_date", "value" => "null", "type" => "single_line_text_field", "namespace" => "Quilt_Updates"],
                    ["key" => "customer_service_img_url", "value" => "null", "type" => "single_line_text_field", "namespace" => "Quilt_Updates"],
                ]
            ]
        ];

        try {
            $this->client->request('PUT', "$this->url/orders/$order->shopify_id.json", ['json' => $body]);
        } catch (GuzzleException $e) {
            Log::error('Error setting metafields', ['error' => $e->getMessage()]);
        }
    }
}
