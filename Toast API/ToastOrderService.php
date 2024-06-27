<?php

namespace App\Services\Toast;


use App\Models\Franchise;
use App\Models\OutgoingCall;
use App\Models\Toast\ToastDiningOption;
use App\Models\Toast\ToastOrder;
use App\Models\Toast\ToastSalesCategory;
use App\Models\Toast\ToastServiceCharge;
use DateTime;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class ToastOrderService
 * @package App\Services
 */
class ToastOrderService
{


    private ToastModifierService $toastModifierService;
    private ToastPaymentService $toastPaymentService;
    private ToastService $toastService;

    public function __construct(
        ToastService $toastService,
        ToastModifierService $toastModifierService,
        ToastPaymentService $toastPaymentService,
    ) {
        $this->toastModifierService = $toastModifierService;
        $this->toastPaymentService = $toastPaymentService;
        $this->toastService = $toastService;
    }

    public function getOrders($startDate, $endDate, $restaurantExternalId, $link = null): bool|array
    {
        $uri = "orders/v2/ordersBulk?startDate=$startDate&endDate=$endDate&pageSize=100";
        if ($link) {
            $uri = $link;
        }
        try {
            $response = $this->toastService->createClient($restaurantExternalId)->get($uri);
            $orders = json_decode($response->getBody());
            $links = $response->getHeader('link');
            $links = implode(',', $links);
            OutgoingCall::create([
                'endpoint' => "orders/v2/ordersBulk?startDate=$startDate&endDate=$endDate&pageSize=100",
                'notes' => "toast",
                'location' => 'ToastOrderService/getOrders'
            ]);
        } catch (GuzzleException $e) {
            Log::error('Error getting Toast Orders', ['error' => $e->getMessage()]);
            return false;
        }
        return ['orders' => $orders, 'links' => $links, 'restaurantExternalId' => $restaurantExternalId];
    }

    public function getCashouts($businessDate, $restaurantExternalId, $link = null): bool|array
    {
        $uri = "cashmgmt/v1/entries?businessDate=$businessDate";
        if ($link) {
            $uri = $link;
        }
        try {
            $response = $this->toastService->createClient($restaurantExternalId)->get($uri, [
                'headers' => [
                    'Toast-Restaurant-External-id' => $restaurantExternalId
                ]
            ]);
            $orders = json_decode($response->getBody());
            $links = $response->getHeader('link');
            $links = implode(',', $links);
            OutgoingCall::create([
                'endpoint' => "cashmgmt/v1/entries?businessDate=$businessDate",
                'notes' => "toast",
                'location' => 'ToastOrderService/getOrders'
            ]);
        } catch (GuzzleException $e) {
            Log::error('Error getting Toast Orders', ['error' => $e->getMessage()]);
            return false;
        }
        return ['orders' => $orders, 'links' => $links, 'restaurantExternalId' => $restaurantExternalId];
    }

    public function saveOrders(array $orders, $restaurantExternalId)
    {
        $modifiers = ['modifiers' => []];
        $payments = [];
        $upsertBody = [];
        foreach ($orders as $order) {
            $paidDate = strtotime($order->paidDate);
            if ($paidDate > 0) {
                $paidDateMinusSix = date("Y-m-d H:i:s", $paidDate - (6 * 60 * 60));
            } else {
                $paidDateMinusSix = date("Y-m-d H:i:s", $paidDate + (60));
            }

            $openedDate = strtotime($order->openedDate);
            if ($openedDate > 0) {
                $openedDateMinusSix = date("Y-m-d H:i:s", $openedDate - (6 * 60 * 60));
            } else {
                $openedDateMinusSix = date("Y-m-d H:i:s", $openedDate + (60));
            }
            foreach ($order->checks as $check) {
                foreach ($check->payments as $payment) {
                    $payments[] = $payment;
                }

                foreach ($check->selections as $selection) {
                    $upsertBody[] = [
                        'selection_guid' => $selection->guid,
                        'guid' => $order->guid,
                        'entity_type' => $order->entityType,
                        'server_guid' => $order->server?->guid ?? null,
                        'server_entity_type' => $order->server?->entityType ?? null,
                        'last_modified_device_id' => $order->lastModifiedDevice->id,
                        'source' => $order->source,
                        'duration' => $order->duration,
                        'business_date' => $order->businessDate,
                        'paid_date' => $paidDateMinusSix,
                        'opened_date' => $openedDateMinusSix,
                        'created_in_test_mode' => $order->createdInTestMode ?? false,
                        'restaurant_guid' => $order->restaurantService->guid ?? null,
                        'voided' => $selection->voided,
                        'delivery_info' => json_encode($order->deliveryInfo),
                        'service_area' => $order->serviceArea,
                        'selection_entity_type' => $selection->entityType,
                        'pre_discounted_price' => $selection->preDiscountPrice,
                        'option_group' => json_encode($selection->optionGroup),
                        'display_name' => $selection->displayName,
                        'appliedDiscounts' => json_encode($selection->appliedDiscounts),
                        'price' => $selection->price,
                        'applied_taxes' => json_encode($selection->appliedTaxes),
                        'quantity' => $selection->quantity,
                        'tax' => $selection->tax,
                        'customer_guid' => $check->customer->guid ?? null,
                        'external_restaurant_guid' => $restaurantExternalId,
                        'dining_option_guid' => $order->diningOption?->guid ?? null,
                        'sales_category_guid' => $selection->salesCategory?->guid ?? null,
                        'applied_service_charges' => json_encode($check->appliedServiceCharges ?? null)
                    ];
                    foreach ($selection->modifiers as $modifier) {
                        $modifier = (array) $modifier;
                        $modifier['selection_guid'] = $selection->guid;
                        $modifiers['modifiers'][] = $modifier;
                    }
                    $modifiers['selection_guid'] = $selection->guid;
                }
            }
            if (count($modifiers['modifiers']) != 0) {
                try {
                    $this->toastModifierService->saveModifiers($modifiers);
                } catch (\Exception $e) {
                    Log::error('error saving toast modifiers', [$e->getMessage(), $e->getLine()]);
                }
            }

            if (count($payments)) {
                $this->toastPaymentService->savePayments($payments);
            }
        }
        ToastOrder::upsert(
            $upsertBody,
            ['selection_guid'],
            [
                'guid',
                'entity_type',
                'server_guid',
                'server_entity_type',
                'last_modified_device_id',
                'source',
                'duration',
                'business_date',
                'opened_date',
                'opened_date',
                'created_in_test_mode',
                'restaurant_guid',
                'voided',
                'delivery_info',
                'service_area',
                'selection_entity_type',
                'pre_discounted_price',
                'option_group',
                'display_name',
                'appliedDiscounts',
                'price',
                'applied_taxes',
                'quantity',
                'tax',
                'customer_guid',
                'external_restaurant_guid',
                'dining_option_guid',
                'sales_category_guid',
                'applied_service_charges'
            ]
        );
    }

    public function getRevenueByMonth($month, $year)
    {
        $activeFranchise = session('activeFranchise');

        $monthlySales = ToastOrder::selectRaw('sum(price + tax) as revenue , min(date(opened_date)) as date')
            ->where('external_restaurant_guid', $activeFranchise->toastRestaurant->restaurant_guid)
            ->groupByRaw('month(opened_date)')
            ->groupByRaw('year(opened_date)')
            ->get();

        return $monthlySales;
    }

    public function getFranchiseWeeklyRevenueForBilling(Franchise $franchise, $lastDayOfWeek)
    {
        $endDate = new DateTime($lastDayOfWeek);
        $endDate->modify("-5 days");
        $endDateString = $endDate->format('Y-m-d');

        $startDate = new DateTime($lastDayOfWeek); // For today/now, don't pass an arg.
        $startDate->modify("-12 days");
        $startDateString = $startDate->format('Y-m-d');
        $sales = ToastOrder::selectRaw('sum(price) as revenue , min(date(opened_date)) as date')
            ->where('external_restaurant_guid', $franchise->toastRestaurant->restaurant_guid)
            ->whereBetween('opened_date', [$startDate, $endDate])
            ->where('voided', 0)
            ->where('display_name', '!=', 'Gift Card')
            ->where('created_in_test_mode', '!=', true)
            ->first();
        return $sales;
    }

    public function getCookiesSoldThisWeek(): array
    {
        $today = date("Y-m-d");
        $startDate = date("Y-m-d", strtotime("last Friday"));
        $activeFranchise = session('activeFranchise');
        $sql = "select
                tm.display_name as modName,
                sum(to2.quantity) as units
                from toast_modifiers tm
                left join toast_orders to2
                on tm.selection_guid = to2.selection_guid
                where date(opened_date) between date('$startDate') and date('$today')
                and to2.external_restaurant_guid = '" . $activeFranchise->toastRestaurant->restaurant_guid . "'
                group by tm.display_name
                having units > 2
                order by units desc";
        return DB::select($sql);
    }

    public function getCookiesSoldByWeek()
    {
        $activeFranchise = session('activeFranchise');
        $sql = "select
                    max(date(opened_date)) as opened_date,
                    tm.display_name as modName,
                    sum(tm.quantity) as units
                from
                    toast_modifiers tm
                    left join toast_orders to2 on tm.selection_guid = to2.selection_guid
                where
                    to2.external_restaurant_guid = '" . $activeFranchise->toastRestaurant->restaurant_guid . "'
                    and DATEDIFF(NOW(), date(opened_date)) < 21
                group by
                    tm.display_name, week(DATE_SUB(date(opened_date), INTERVAL 5 DAY))
                having
                    units > 2;";
        $results = DB::select($sql);
        $weeklySales = [];
        foreach ($results as $result) {
            $friday = $result->opened_date;
            if (date('w', strtotime($result->opened_date)) != 'Friday') {
                $friday = date('y-m-d', strtotime('last friday', strtotime($result->opened_date)));
            }
            $weeklySales[$friday][$result->modName] = $result->units;
        }
        return $weeklySales;
    }

    public function getRevenueByWeek($chargeDate)
    {
        $endDate = new DateTime($chargeDate);
        $endDate->modify("-6 days");
        $endDateString = $endDate->format('Y-m-d');

        $startDate = new DateTime($chargeDate); // For today/now, don't pass an arg.
        $startDate->modify("-12 days");
        $startDateString = $startDate->format('Y-m-d');
        //dd($startDateString . " " . $endDateString);
        $sales = ToastOrder::selectRaw('sum(price) as revenue , min(date(opened_date)) as date')
            ->whereBetween('opened_date', [$startDate, $endDate])
            ->where('voided', 0)
            ->where('display_name', '!=', 'Gift Card')
            ->where('created_in_test_mode', '!=', true)
            ->first();
        return $sales;
    }

    public function getSalesForDailyEmail($date)
    {
        $date = $date->subDays(1);
        $mysqlDate = $date->format('Y-m-d');

        $monthlySales = ToastOrder::selectRaw('sum(price) as monthly_sales')
            ->selectRaw("DATE_FORMAT(opened_date , '%m-%Y') as sales_month")
            ->where('voided', 0)
            ->where('display_name', '!=', 'Gift Card')
            ->where('created_in_test_mode', '!=', true)
            ->groupByRaw("DATE_FORMAT(opened_date , '%m-%Y')")
            ->orderBy('opened_date', 'desc')
            ->limit(13)
            ->get();

        $dailySales = ToastOrder::selectRaw('sum(price) as daily_sales')
            ->selectRaw("DATE_FORMAT(opened_date , '%m-%d-%Y') as sales_date")
            ->where('voided', 0)
            ->where('display_name', '!=', 'Gift Card')
            ->where('created_in_test_mode', '!=', true)
            ->groupByRaw("DATE_FORMAT(opened_date , '%m-%d-%Y')")
            ->orderBy('opened_date', 'desc')
            ->limit(31)
            ->get();
        $perStoreSalesData = [];
        $franchises = Franchise::all();
        foreach ($franchises as $franchise) {
            if ($franchise->toastRestaurant) {
                $perStoreSalesData[] = [
                    "franchise" => $franchise,
                    "location_name" => "$franchise->line_1, $franchise->city, $franchise->state",
                    "sales" => $franchise->toastRestaurant->orders()->whereDate('opened_date', $mysqlDate)->sum('price')
                ];
            }
        }

        $salesData = [
            'monthlyTotals' => $monthlySales,
            'dailyTotals' => $dailySales,
            'perStoreTotals' => $perStoreSalesData
        ];

        return $salesData;
    }

    public function saveDiningOptions($diningOptions)
    {
        foreach ($diningOptions as $diningOption) {
            ToastDiningOption::updateOrCreate(
                [
                    'guid' => $diningOption->guid
                ],
                [
                    'name' => $diningOption->name,
                    'curbside' => $diningOption->curbside,
                    'behavior' => $diningOption->behavior
                ]
            );
        }
    }

    public function getDiningOptions()
    {
        $uri = "config/v2/diningOptions";
        $restaurantExternalId = config('app.toast_restaurant_external_id');
        try {
            $response = $this->toastService->createClient($restaurantExternalId)->get($uri, [
                'headers' => [
                    'Toast-Restaurant-External-id' => $restaurantExternalId
                ]
            ]);
            $diningOptions = json_decode($response->getBody());
            OutgoingCall::create([
                'endpoint' => "config/v2/diningOptions",
                'notes' => "toast",
                'location' => 'ToastOrderService/getDiningOptions'
            ]);
        } catch (GuzzleException $e) {
            Log::error('Error getting Toast Dining Options', ['error' => $e->getMessage()]);
            return false;
        }
        return ['diningOptions' => $diningOptions, 'links' => [], 'restaurantExternalId' => $restaurantExternalId];
    }

    public function getSalesCategories()
    {
        $uri = "config/v2/salesCategories";
        $restaurantExternalId = config('app.toast_restaurant_external_id');
        try {
            $response = $this->toastService->createClient($restaurantExternalId)->get($uri, [
                'headers' => [
                    'Toast-Restaurant-External-id' => $restaurantExternalId
                ]
            ]);
            $salesCategories = json_decode($response->getBody());
            OutgoingCall::create([
                'endpoint' => "config/v2/salesCategories",
                'notes' => "toast",
                'location' => 'ToastOrderService/getSalesCategories'
            ]);
        } catch (GuzzleException $e) {
            Log::error('Error getting Toast Sales Categories', ['error' => $e->getMessage()]);
            return false;
        }
        return ['salesCategories' => $salesCategories, 'links' => [], 'restaurantExternalId' => $restaurantExternalId];
    }

    public function saveSalesCategories($salesCategories)
    {
        foreach ($salesCategories as $category) {
            ToastSalesCategory::updateOrCreate(
                [
                    'guid' => $category->guid
                ],
                [
                    'name' => $category->name
                ]
            );
        }
    }

    public function getServiceCharges()
    {
        $uri = "config/v2/serviceCharges";
        $restaurantExternalId = config('app.toast_restaurant_external_id');
        try {
            $response = $this->toastService->createClient($restaurantExternalId)->get($uri, [
                'headers' => [
                    'Toast-Restaurant-External-id' => $restaurantExternalId
                ]
            ]);
            $serviceCharges = json_decode($response->getBody());
            OutgoingCall::create([
                'endpoint' => "config/v2/serviceCharges",
                'notes' => "toast",
                'location' => 'ToastOrderService/getServiceCharges'
            ]);
        } catch (GuzzleException $e) {
            Log::error('Error getting Toast Service Charges', ['error' => $e->getMessage()]);
            return false;
        }
        return ['serviceCharges' => $serviceCharges, 'links' => [], 'restaurantExternalId' => $restaurantExternalId];
    }

    public function saveServiceCharges($serviceCharges)
    {
        foreach ($serviceCharges as $charge) {
            ToastServiceCharge::updateOrCreate(
                [
                    'guid' => $charge->guid
                ],
                [
                    'name' => $charge->name ?? null,
                    'amount_type' => $charge->amountType ?? null,
                    'amount' => $charge->amount ?? null,
                    'taxable' => $charge->taxable ?? null,
                    'criteria' => json_encode($charge->criteria ?? null),
                    'destination' => $charge->destination,
                    'gratuity' => $charge->gratuity,
                    'percent' => $charge->percent
                ]
            );
        }
    }
}
