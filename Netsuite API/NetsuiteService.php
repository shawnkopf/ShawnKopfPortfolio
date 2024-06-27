<?php

namespace App\Services\Netsuite;

use App\Models\ConnectionDetail;
use App\Models\Netsuite\NetsuiteCustomer;
use App\Models\Netsuite\NetsuiteItem;
use App\Models\Netsuite\NetsuiteTransactionItem;
use DateTimeImmutable;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Encoding\ChainedFormatter;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Signer\Ecdsa\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Token\Builder;

class NetsuiteService
{
    public string $accountId;

    public function __construct()
    {
        $this->accountId = config('app.netsuite_account_id');
    }

    public function getJwt()
    {
        $configuration = Configuration::forAsymmetricSigner(
            new Sha256(),
            InMemory::file('private-key.pem'),
            InMemory::file('public-key.pem'),
        );

        $tokenBuilder = (new Builder(new JoseEncoder(), ChainedFormatter::default()));
        $algorithm = new Sha256();
        $aud = "https://$this->accountId.suitetalk.api.netsuite.com/services/rest/auth/oauth2/v1/token";
        $now = new DateTimeImmutable();

        $token = $tokenBuilder
            ->withHeader('typ', 'JWT')
            ->withHeader('alg', 'ES256')
            ->issuedBy(config('app.netsuite_client_id'))
            ->permittedFor($aud)
            ->issuedAt($now)
            ->expiresAt($now->modify('+1 hour'))
            ->withHeader('kid', config('app.netsuite_certificate_id'))
            ->withClaim('scope', 'restlets, rest_webservices')
            ->getToken($algorithm, $configuration->signingKey());

        return $token->toString();
    }

    public function getBearerToken()
    {
        $bearerToken = ConnectionDetail::where('detail_name', 'netsuite_bearer_token')->first();
        if ($bearerToken) {
            if ((time() - strtotime($bearerToken->updated_at)) < 3500) {
                return $bearerToken->detail_value;
            }
        }

        $client = new Client();
        $jwt = $this->getJwt();
        $url = "https://$this->accountId.suitetalk.api.netsuite.com/services/rest/auth/oauth2/v1/token";
        $request = $client->post($url, [
            'form_params' => [
                'grant_type' => 'client_credentials',
                'client_assertion_type' => 'urn:ietf:params:oauth:client-assertion-type:jwt-bearer',
                'client_assertion' => $jwt
            ]
        ]);

        $response = json_decode($request->getBody()->getContents(), true);

        ConnectionDetail::updateOrCreate(
            [
                'connection_name' => 'netsuite',
                'detail_name' => 'netsuite_bearer_token'
            ],
            [
                'detail_value' => $response['access_token']
            ]
        );

        return $response['access_token'];
    }

    public function makeNsQuery($q, $url)
    {
        $client = new Client();
        $token = $this->getBearerToken();
        $request = $client->post($url, [
            'body' => json_encode($q),
            'headers' => [
                'Authorization' => "Bearer $token",
                'Prefer' => 'Transient',
            ]
        ]);

        return json_decode($request->getBody()->getContents(), true);
    }

    public function getCustomers($url)
    {
        $q = [
            'q' => "SELECT defaultbillingaddress, BUILTIN.DF(Customer.category) AS category, companyname, custentity_store_addr, custentity_bdc_shortname, custentity_store_city, custentity_store_state, custentity_store_zip, balancesearch, consoldaysoverduesearch, consoloverduebalancesearch, consolunbilledorderssearch, BUILTIN.DF(Customer.custentity80) AS tier, BUILTIN.DF(Customer.globalSubscriptionStatus) AS globalSubscriptionStatus, id, isinactive, BUILTIN.DF(Customer.priceLevel) AS priceLevel, BUILTIN.DF(Customer.salesRep) AS salesRep, salesrep, BUILTIN.DF(Customer.sourceWebSite) AS sourceWebSite, BUILTIN.DF(Customer.terms) AS terms, visits, email, datecreated, BUILTIN.DF(Customer.entityStatus) AS storeStatus, BUILTIN.DF(Customer.custentity85) AS firstOrderDate FROM Customer"
        ];

        return $this->makeNsQuery($q, $url);
    }

    public function saveCustomers($customers)
    {
        $upsertBody = [];
        $upsertFields = [
            'category',
            'company_name',
            'billing_line_1',
            'billing_entity_name',
            'billing_attention',
            'billing_city',
            'billing_county',
            'billing_state',
            'billing_zip',
            'aging',
            'aging1',
            'aging2',
            'aging3',
            'aging4',
            'balance',
            'days_overdue',
            'overdue_balance',
            'unbilled_orders',
            'tier',
            'backorder_count',
            'global_subscription_status',
            'is_inactive',
            'price_level',
            'sales_rep_name',
            'sales_rep_id',
            'source_website',
            'terms',
            'visits',
            'email',
            'ns_date_created',
            'store_status',
            'first_order_date',
            'ns_customer_id',
            'default_billing_address'
        ];
        foreach ($customers as $customer) {
            $customer = (object) $customer;
            $upsertBody[] = [
                'category' => $customer->category ?? null,
                'company_name' => $customer->companyname ?? null,
                'billing_line_1' => $customer->custentity_store_addr ?? null,
                'billing_entity_name' => $customer->custentity_bdc_shortname ?? null,
                'billing_attention' => null,
                'billing_city' => $customer->custentity_store_city ?? null,
                'billing_county' => null,
                'billing_state' => $customer->custentity_store_state ?? null,
                'billing_zip' => $customer->custentity_store_zip ?? null,
                'aging' => null,
                'aging1' => null,
                'aging2' => null,
                'aging3' => null,
                'aging4' => null,
                'balance' => $customer->balancesearch ?? null,
                'days_overdue' => $customer->consoldaysoverduesearch ?? null,
                'overdue_balance' => $customer->consoloverduebalancesearch ?? null,
                'unbilled_orders' => $customer->consolunbilledorderssearch ?? null,
                'tier' => $customer->tier ?? null,
                'backorder_count' => null,
                'global_subscription_status' => $customer->globalsubscriptionstatus ?? null,
                'ns_customer_id' => $customer->id,
                'is_inactive' => null,
                'price_level' => $customer->pricelevel ?? null,
                'sales_rep_name' => $customer->salesrepname ?? null,
                'sales_rep_id' => $customer->salesrep ?? null,
                'source_website' => $customer->sourcewebsite ?? null,
                'terms' => $customer->terms ?? null,
                'visits' => $customer->visits ?? null,
                'email' => $customer->email ?? null,
                'ns_date_created' => date('Y-m-d', strtotime($customer->datecreated ?? null)),
                'store_status' => $customer->storestatus ?? null,
                'first_order_date' => isset($customer->firstorderdate) ? date('Y-m-d', strtotime($customer->firstorderdate)) : null,
                'default_billing_address' => $customer->defaultbillingaddress ?? null
            ];
        }
        NetsuiteCustomer::upsert($upsertBody, ['ns_customer_id'], $upsertFields);
        return true;
    }
}
