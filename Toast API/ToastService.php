<?php

namespace App\Services\Toast;


use App\Models\OutgoingCall;
use App\Services\ProductService;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Phpml\Regression\LeastSquares;

/**
 * Class ToastService
 * @package App\Services
 */
class ToastService
{
    /**
     * @var ProductService
     */
    public ProductService $productService;

    /**
     * @var Client
     */
    private Client $client;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function createClient($toastExternalRestaurantGuid): Client
    {
        $token = $this->getToastToken($toastExternalRestaurantGuid);
        return new Client(
            [
                'base_uri' => config('app.toast_url'),
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Toast-Restaurant-External-ID' => $toastExternalRestaurantGuid
                ]
            ]
        );
    }

    /**
     * @return bool
     */
    public function getToastToken($toastExternalRestaurantGuid = null)
    {
        $token = Redis::get("toast_token");
        $tokenDate = Redis::get('toast_token_date');
        $tokenSeconds = time() - $tokenDate;
        if (!$token || $tokenSeconds > 86340) {
            $headers = [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ];

            if ($toastExternalRestaurantGuid) {
                $headers['Toast-Restaurant-External-ID'] = $toastExternalRestaurantGuid;
            }
            $client = new Client(
                [
                    'base_uri' => config('app.toast_url'),
                    'headers' => $headers
                ]
            );
            $body = [
                'clientId' => config('app.toast_client_id'),
                'clientSecret' => config('app.toast_client_secret'),
                'userAccessType' => config('app.toast_user_access_type'),
            ];
            try {
                $request = $client->post("authentication/v1/authentication/login", [
                    'json' => $body
                ]);
                OutgoingCall::create([
                    'endpoint' => "authentication/v1/authentication/login",
                    'notes' => "toast",
                    'location' => 'ToastService/getToastToken'
                ]);
            } catch (GuzzleException $e) {
                Log::alert('Toast: Get Token Error', ['error' => $e]);
                return false;
            }

            $toastToken = json_decode($request->getBody())->token->accessToken;
            if ($toastToken == Redis::get('toast_token')) {
                return $toastToken;
            }
            Redis::set("toast_token", $toastToken);
            Redis::set('toast_token_date', time());
            return $toastToken;
        }
        return $token;
    }

    public function cookiesSoldByMonth($startDate, $months, $externalRestaurantId = null)
    {
        $endDate = $startDate - $months;
    }

    public function predictCookieSalesByName($cookieName, $date, $toastRestaurantGuid)
    {
        $salesByWeek = DB::table('toast_orders')
            ->join('toast_modifiers', 'toast_orders.selection_guid', '=', 'toast_modifiers.selection_guid')
            ->selectRaw("min(STR_TO_DATE(business_date,'%Y%m%d')) as order_date, toast_modifiers.display_name,sum(toast_modifiers.quantity) as units_sold")
            ->where('toast_modifiers.display_name', $cookieName)
            ->where('toast_orders.external_restaurant_guid', $toastRestaurantGuid)
            ->groupByRaw("toast_modifiers.display_name, week(STR_TO_DATE(business_date, '%Y%m%d')), YEAR(STR_TO_DATE(business_date,'%Y%m%d'))")
            ->get();

        $sales = [];

        foreach ($salesByWeek as $week) {
            $weeks[] = [strtotime($week->order_date)];
            $sales[] = $week->units_sold;
        }

        $prediction = 0;

        if (isset($weeks) && count($weeks) > 1) {
            $regression = new LeastSquares();
            $regression->train($weeks, $sales);
            $prediction = ($regression->predict([strtotime($date)]));
        }

        return $prediction;
    }

    public function predictWeeklyCookieSales($weekStartDate, $cookieName, $toastRestaurantGuid)
    {
        return $this->predictCookieSalesByName($cookieName, $weekStartDate, $toastRestaurantGuid);
    }

    public function predictTotalWeeklyCookieSales($date, $toastRestaurantGuid)
    {
        $salesByWeek = DB::table('toast_orders')
            ->join('toast_modifiers', 'toast_orders.selection_guid', '=', 'toast_modifiers.selection_guid')
            ->selectRaw("min(STR_TO_DATE(business_date,'%Y%m%d')) as order_date, sum(toast_modifiers.quantity) as units_sold")
            ->where('toast_orders.external_restaurant_guid', $toastRestaurantGuid)
            ->groupByRaw("week(STR_TO_DATE(business_date, '%Y%m%d')), YEAR(STR_TO_DATE(business_date,'%Y%m%d'))")
            ->get();

        $sales = [];

        foreach ($salesByWeek as $week) {
            $weeks[] = [strtotime($week->order_date)];
            $sales[] = $week->units_sold;
        }

        $prediction = 0;

        if (isset($weeks)) {
            $regression = new LeastSquares();
            $regression->train($weeks, $sales);
            $prediction = ($regression->predict([strtotime($date)]));
        }

        return $prediction;
    }
}
