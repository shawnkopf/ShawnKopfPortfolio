<?php

namespace App\Services\Toast;


use App\Models\OutgoingCall;
use App\Models\Toast\ToastEmployee;
use App\Models\Toast\ToastRestaurant;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

/**
 * Class ToastRestaurantService
 * @package App\Services
 */
class ToastRestaurantService
{
    /**
     * @var Client
     */
    private Client $client;


    public function __construct(
        public ToastService $toastService,
    ) {
         $token = $toastService->getToastToken(config('app.toast_restaurant_external_id'));
        //$token = "eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXVCIsImtpZCI6Ik9FSkZNRUZEUlRkRVEwWkJPVFkwTWprNU9UUkRNVFZHUXpFMlJURkdNalUzUVRjeE16TTFNUSJ9.eyJodHRwczovL3RvYXN0dGFiLmNvbS9jbGllbnRfbmFtZSI6IkNBQTY1Q0IwMUYzNUNFRDQiLCJodHRwczovL3RvYXN0dGFiLmNvbS9hY2Nlc3NfdHlwZSI6IlRPQVNUX01BQ0hJTkVfQ0xJRU5UIiwiaHR0cHM6Ly90b2FzdHRhYi5jb20vZXh0ZXJuYWxfaWQiOiJDQUE2NWNiMDFmMzVjZWQ0IiwiaHR0cHM6Ly90b2FzdHRhYi5jb20vcGFydG5lcl9ndWlkIjoiYmJlMjg1MzMtMmRkOS00ZjYwLWE5YTMtN2FiYjc2NWVjZDYzIiwiaHR0cHM6Ly90b2FzdHRhYi5jb20vdHlwZSI6IklOREVQRU5ERU5UX1BBUlRORVIiLCJpc3MiOiJodHRwczovL3RvYXN0LXBvcy50b2FzdHRhYi5hdXRoMC5jb20vIiwic3ViIjoiWlZQaEQ4Yk9Td0VieXR6cU92aWxjZmJZelRUNXF2d0NAY2xpZW50cyIsImF1ZCI6Imh0dHBzOi8vdG9hc3Qtc2VydmljZXMtYXBpLyIsImlhdCI6MTcxNTAyMDIwMywiZXhwIjoxNzE1MTA2NjAzLCJzY29wZSI6ImNhc2htZ210OnJlYWQgY29uZmlnOnJlYWQgbGFib3I6cmVhZCBtZW51czpyZWFkIG9yZGVyczpyZWFkIHJlc3RhdXJhbnRzOnJlYWQgZ3Vlc3QucGk6cmVhZCBsYWJvci5lbXBsb3llZXM6cmVhZCIsImd0eSI6ImNsaWVudC1jcmVkZW50aWFscyIsImF6cCI6IlpWUGhEOGJPU3dFYnl0enFPdmlsY2ZiWXpUVDVxdndDIn0.WCo0RQRAdfP2ecPvQuavD8OjmfHxVMlcXHblD6ZBoFzIHoI1eIX2FE2w2P7uVXY48aNHRgBPj6YqK1e01lnrqEOxn28f0_T7_z4wMVceoFoch0kEt29OMlE88hTdv2l-j8k5K-DlVd8yyfo4F3A22wq0FZFf_KUiQ_a3tqE2E1EJkygiIUlyXK1XQzr59W0yhqsmIiqyCMip67YyugBuhuSDa-yOGRkJ8BePjMTvIsMGsFFLOhbBduUHtyf4m1m3p7YmofK42ziymiLhvSOeWusC93yXI0pBrhkltsUtsZ2WmDY5RdUydSrXk9mohK9BuZqVfsmt0zcqLUIC7tvyJw";
        $this->client = new Client(
            [
                'base_uri' => config('app.toast_url'),
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Toast-Restaurant-External-id' => config('app.toast_restaurant_external_id')
                ]
            ]
        );
    }

    /**
     * @return bool|array
     */
    public function getRestaurants(): bool|array
    {
        $uri = "/partners/v1/restaurants";
        try {
            $response = $this->client->get($uri);
            $restaurants = json_decode($response->getBody());
            OutgoingCall::create([
                'endpoint' => "/partners/v1/restaurants",
                'notes' => "toast",
                'location' => 'ToastRestaurantService/getRestaurants'
            ]);
        } catch (GuzzleException $e) {
            Log::error('Error getting Toast Restaurants', ['error' => $e->getMessage()]);
            return false;
        }
        return $restaurants;
    }

    public function saveRestaurants(array $restaurants)
    {
        $upsertBody = [];
        foreach ($restaurants as $restaurant) {
            $upsertBody[] = [
                'restaurant_guid' => $restaurant->restaurantGuid,
                'management_group_guid' => $restaurant->managementGroupGuid,
                'restaurant_name' => $restaurant->restaurantName,
                'location_name' => $restaurant->locationName,
                'external_group_ref' => $restaurant->externalGroupRef,
                'external_restaurant_ref' => $restaurant->externalRestaurantRef,
            ];
        }

        ToastRestaurant::upsert(
            $upsertBody,
            ['restaurant_guid'],
            [
                'management_group_guid',
                'restaurant_name',
                'location_name',
                'external_group_ref',
                'external_restaurant_ref'
            ]
        );
    }

    public function getEmployees(ToastRestaurant $toastRestaurant)
    {
        $restaurantGuid = $toastRestaurant->restaurant_guid;
        $uri = "labor/v1/employees";
        try {
            $response = $this->toastService->createClient($restaurantGuid)->get($uri, [
                'headers' => [
                    'Toast-Restaurant-External-id' => $restaurantGuid
                ]
            ]);
            $employees = json_decode($response->getBody());
            OutgoingCall::create([
                'endpoint' => "labor/v1/employees",
                'notes' => "toast",
                'location' => 'ToastRestaurantService/getEmployees'
            ]);
        } catch (GuzzleException $e) {
            Log::error('Error getting Toast Employees', ['error' => $e->getMessage()]);
            return false;
        }
        return ['employees' => $employees, 'links' => [], 'restaurantExternalId' => $restaurantGuid];
    }

    public function saveEmployees($employees)
    {
        if (!$employees) {
            return;
        }
        foreach ($employees['employees'] as $employee) {
            ToastEmployee::updateOrCreate(
                [
                    'guid' => $employee->guid,
                    'toast_restaurant_guid' => $employees['restaurantExternalId'],
                ],
                [
                    'entity_type' => $employee->entityType ?? null,
                    'v2_guid' => $employee->v2EmployeeGuid ?? null,
                    'last_name' => $employee->lastName ?? null,
                    'first_name' => $employee->firstName ?? null,
                    'deleted' => $employee->deleted ?? null,
                    'email' => $employee->email ?? null,
                    'created_date' => strtotime($employee->createdDate),
                    'toast_restaurant_guid' => $employees['restaurantExternalId'],
                    'job_references' => json_encode($employee->jobReferences ?? null),
                    'wage_overrides' => json_encode($employee->wageOverrides ?? null)
                ]
            );
        }
    }
}
