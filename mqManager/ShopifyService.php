<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Shopify\ShopifyOrder;
use App\Models\ShopifyDiscountCode;
use Graviton\LinkHeaderParser\LinkHeader;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

class ShopifyService
{
    public Client $client;

    public OrderService $orderService;

    public QuiltService $quiltService;

    public function __construct(
        OrderService $orderService,
        QuiltService $quiltService
    )
    {
        $this->orderService = $orderService;
        $this->quiltService = $quiltService;

        $this->client = new Client([
            'headers' => ['X-Shopify-Access-Token' => config('shopify.shopify_admin_access_token')],
            'base_uri' => config('shopify.shopify_store_url')
        ]);
    }

    public function syncOrders(): bool
    {
        //$nextUrl = '2022-04/orders.json?status=any&limit=250';
        $nextUrl="2022-04/orders.json?since_id=0&limit=250&status=any";
        $createdAtMin = Order::max('shopify_created_at');
        if($createdAtMin) {
            $createdAtMin = date('c', strtotime($createdAtMin. "-3 days"));
            $nextUrl="2022-04/orders.json?since_id=0&limit=250&status=any&created_at_min=$createdAtMin";
        }
        do {
            try {
                $request = $this->client->get($nextUrl);
            } catch (GuzzleException $e) {
                Log::error('Error syncing orders', [$nextUrl]);
                return false;
            }
            $response = json_decode($request->getBody(), true);
            foreach ($response['orders'] as $order) {
                echo($order['created_at'] . PHP_EOL);
                $this->orderService->consumeOrderWebhook($order);
                $this->quiltService->createQuiltFromWebhook($order);
            }
            $linkHeader = LinkHeader::fromString($request->getHeaders()['Link'][0]);
            $nextUrl = null;
            if ($linkHeader->getRel('next')) {
                $nextUrl = $linkHeader->getRel('next')->getUri();
            }
        } while ($nextUrl);
        return true;
    }

    public function getDiscountCodes(): array
    {
        $priceRulesRequest = $this->client->get('2022-04/price_rules.json');
        $priceRules = json_decode($priceRulesRequest->getBody(), true);
        $allDiscountCodes = [];
        foreach ($priceRules['price_rules'] as $rule) {
            $ruleId = $rule['id'];
            $discountCodesRequest = $this->client->get("2022-04/price_rules/$ruleId/discount_codes.json");
            $discountCodes = json_decode($discountCodesRequest->getBody(), true);
            foreach ($discountCodes['discount_codes'] as $code) {
                $allDiscountCodes[strtolower($code['code'])] = ['code' => $code, 'rules' => $rule];
            }
        }

        return($allDiscountCodes);
    }

    public function saveDiscountCodes($discountCodes)
    {
        foreach ($discountCodes as $name => $code) {
            ShopifyDiscountCode::updateOrCreate(
                [
                    'name' => $name
                ],
                [
                    'ends_at' => $code['rules']['ends_at'],
                    'starts_at' => $code['rules']['starts_at'],
                    'value_type' => $code['rules']['value_type'],
                    'value' => $code['rules']['value']
                ]);
        }
    }
}
