<?php


namespace App\Services;


use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Netsuite\NetsuiteCustomer;
use Exception;

class KlaviyoService
{
    protected $apiKey;
    protected $revisionDate;

    public function __construct()
    {

        if (app()->environment('testing') || env('USE_KLAVIYO_TEST_API', false)) {
            $this->apiKey = config('app.test_klaviyo_api_key');
        } else {
            $this->apiKey = config('app.klaviyo_api_key');
        }

        $this->revisionDate = config('app.api_revision_date');
    }

    public function updateProfileWithCustomProperties($profileId, $customProperties)
    {
        $endpoint = "https://a.klaviyo.com/api/profiles/{$profileId}/";

        $data = [
            'data' => [
                'type' => 'profile',
                'id' => $profileId,
                'attributes' => [
                    'properties' => $customProperties
                ]
            ]
        ];

        try {
            $response = Http::withHeaders([
                'Authorization' => "Klaviyo-API-Key {$this->apiKey}",
                'accept' => 'application/json',
                'content-type' => 'application/json',
                'revision' => $this->revisionDate,
            ])->patch($endpoint, $data);

            if ($response->successful()) {
                Log::info("Profile {$profileId} updated successfully.", ['response' => $response->body()]);
                return $response->json();
            } else {
                Log::error("Failed to update profile {$profileId}.", ['response' => $response->body()]);
                return false;
            }
        } catch (\Exception $e) {
            Log::error("Exception encountered while updating profile {$profileId}.", ['exception' => $e->getMessage()]);
            return false;
        }
    }
    public function getProfileById($profileId)
    {
        $endpoint = "https://a.klaviyo.com/api/profiles/{$profileId}";
        try {
            $response = Http::withHeaders([
                'Authorization' => "Klaviyo-API-Key {$this->apiKey}",
                'accept' => 'application/json',
                'revision' => $this->revisionDate,
            ])->get($endpoint);

            return $response->json()['data'];
        } catch (Exception $e) {
            Log::error("Failed to retrieve profile {$profileId}.", ['error' => $e->getMessage()]);
            return null;
        }
    }


    public function trackEvent($eventName, $customerProperties, $eventProperties = [])
    {

        $data = [
            'token' => $this->apiKey,
            'event' => $eventName,
            'customer_properties' => $customerProperties,
            'properties' => $eventProperties,
            'time' => time()
        ];

        $jsonData = json_encode($data);
        $base64Data = base64_encode($jsonData);
        $urlEncodeData = urlencode($base64Data);


        $endpoint = 'https://a.klaviyo.com/api/track' . $urlEncodeData;

        try {

            $response = Http::retry(3, 100)->withHeaders([
                'Content-Type' => 'application/json'
            ])->post($endpoint . base64_encode(json_encode($data)));


            Log::info('Klaviyo trackEvent request successful', ['request' => $data, 'response' => $response->body()]);

            return $response->json();
        } catch (\Exception $e) {
            Log::error('Klaviyo trackEvent error', ['message' => $e->getMessage(), 'data' => $data]);

            return null;
        }
    }

    public function createOrUpdateProfile($profileData, $revisionDate)
    {
        $endpoint = 'https://a.klaviyo.com/api/profile-import/';


        $payload = [
            "data" => [
                "type" => "profile",
                "attributes" => $profileData,
            ],
        ];
        $revisionDate = $this->revisionDate;

        Log::debug('Sending profile creation/update request to Klaviyo.', [
            'endpoint' => $endpoint,
            'payload' => $payload
        ]);

        try {

            $response = Http::withHeaders([
                'Authorization' => "Klaviyo-API-Key {$this->apiKey}",
                'accept' => 'application/json',
                'content-type' => 'application/json',
                'revision' => $revisionDate
            ])->post($endpoint, $payload);


            if ($response->successful()) {
                Log::info('Profile created or updated successfully in Klaviyo.', ['response' => $response->body()]);
                return $response->json();
            } else {
                Log::error('Failed to create or update profile in Klaviyo.', ['response' => $response->body(), 'status' => $response->status()]);
                return false;
            }
        } catch (\Exception $e) {
            Log::error('KlaviyoService createOrUpdateProfile encountered an exception.', ['exception' => $e->getMessage()]);
            return false;
        }
    }

    public function importProfilesBulk(array $profiles, $listId = null)
    {
        $endpoint = 'https://a.klaviyo.com/api/profile-bulk-import-jobs/';

        $payload = [
            "data" => [
                "type" => "profile-bulk-import-job",
                "attributes" => [
                    "profiles" => [
                        "data" => $profiles
                    ]
                ]
            ]
        ];

        if ($listId) {
            $payload['data']['relationships'] = [
                "lists" => [
                    "data" => [[
                        "type" => "list",
                        "id" => $listId
                    ]]
                ]
            ];
        }
        Log::info('Sending bulk profiles import request to Klaviyo.', ['payload' => json_encode($payload)]);

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Klaviyo-API-Key ' . $this->apiKey,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Revision' => $this->revisionDate,
            ])->post($endpoint, $payload);

            if ($response->successful()) {
                Log::info('Bulk profiles imported successfully to Klaviyo.', ['response' => $response->body()]);
                return $response->json();
            } else {
                Log::error('Failed to import bulk profiles to Klaviyo.', ['response' => $response->body()]);
                return false;
            }
        } catch (\Exception $e) {
            Log::error('KlaviyoService importProfilesBulk encountered an exception.', ['exception' => $e->getMessage()]);
            return false;
        }
    }

    public function getKlaviyoProfileIdByEmail($email)
    {
        $emailString = urlencode("equals(email,'$email')");
        $endpoint = "https://a.klaviyo.com/api/profiles?filter=$emailString";

        $revisionDate = $this->revisionDate;

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Klaviyo-API-Key ' . $this->apiKey,
                'accept' => 'application/json',
                'revision' => $revisionDate,
            ])->get($endpoint);

            $data = $response->json();
            if (!empty($data['data']) && is_array($data['data']) && count($data['data']) > 0) {
                $profileId = $data['data'][0]['id'] ?? null;
                return $profileId;
            }
            // }
        } catch (\Exception $e) {
            Log::error('Failed to retrieve Klaviyo profile ID by email.', ['email' => $email, 'error' => $e->getMessage()]);
        }

        return null;
    }

    public function getKlaviyoProfileSubscriptionStatus($email)
    {
        $emailString = urlencode("equals(email,'$email')");
        $endpoint = "https://a.klaviyo.com/api/profiles?filter=$emailString";
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Klaviyo-API-Key ' . $this->apiKey,
                'accept' => 'application/json',
                'revision' => $this->revisionDate,
            ])->get($endpoint);
            $data = $response->json();
            if (!empty($data['data']) && is_array($data['data']) && count($data['data']) > 0) {
                return $data['data'][0]['attributes']['subscriptions'] ?? null;
            }
        } catch (\Exception $e) {
            Log::error('Failed to retrieve Klaviyo profile subscription status.', ['email' => $email, 'error' => $e->getMessage()]);
        }
        return null;
    }
    public function unsubscribeKlaviyoProfile($email)
    {
        $endpoint = 'https://a.klaviyo.com/api/profile-subscription-bulk-delete-jobs/';
        $payload = [
            'data' => [
                'type' => 'profile-subscription-bulk-delete-job',
                'attributes' => [
                    'profiles' => [
                        'data' => [
                            [
                                'type' => 'profile',
                                'attributes' => [
                                    'email' => $email
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Klaviyo-API-Key ' . $this->apiKey,
                'accept' => 'application/json',
                'content-type' => 'application/json',
                'revision' => $this->revisionDate,
            ])->post($endpoint, $payload);
            if ($response->successful()) {
                return $response->json();
            } else {
                Log::error('Failed to unsubscribe profile in Klaviyo.', ['response' => $response->body(), 'status' => $response->status()]);
                return false;
            }
        } catch (\Exception $e) {
            Log::error('KlaviyoService unsubscribeKlaviyoProfile encountered an exception.', ['exception' => $e->getMessage()]);
            return false;
        }
    }

    public function mapCustomerToKlaviyoProperties(NetsuiteCustomer $customer)
    {
        return [
            'email' => $customer->email,
            'organization' => $customer->company_name,
        ];
    }
}
