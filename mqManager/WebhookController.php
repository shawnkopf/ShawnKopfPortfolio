<?php

namespace App\Http\Controllers;

use App\Services\OrderItemService;
use App\Services\OrderService;
use App\Services\QuiltService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    private OrderService $orderService;
    private OrderItemService $orderItemService;
    private QuiltService $quiltService;


    private mixed $shopifyWebhookToken;

    public function __construct(
        OrderService $orderService,
        OrderItemService $orderItemService,
        QuiltService $quiltService

    )
    {
        $this->orderService = $orderService;
        $this->orderItemService = $orderItemService;
        $this->quiltService = $quiltService;

        $this->shopifyWebhookToken = config('app.shopify_admin_webhook_token');
    }
    public function consumeOrderWebhook(Request $request): Response|Application|ResponseFactory
    {
        $verified = $this->verify_webhook($request);
        if (!$verified) {
            Log::critical('Bad webhook', ['request' => $request]);
            abort(422, 'Bad Webhook');
        }

        $requestBody = json_decode($request->getContent(), true);
        $order = $this->orderService->consumeOrderWebhook($requestBody);
        if($order->wasRecentlyCreated) {
            Log::info('Freshly Created!');
            $this->orderService->setNewOrderMetafields($order);
        }
        $this->orderItemService->consumeOrderWebhook($requestBody);
        $this->quiltService->createQuiltFromWebhook($requestBody);

        return response('success', 200);
    }

    /**
     * @param Request $request
     * @return bool
     */
    private function verify_webhook(Request $request): bool
    {
        $hmac_header = $request->header('X_SHOPIFY_HMAC_SHA256');
        $data = $request->getContent();
        $calculated_hmac = base64_encode(hash_hmac('sha256', $data, $this->shopifyWebhookToken, true));
        return hash_equals($hmac_header, $calculated_hmac);
    }
}
