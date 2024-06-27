<?php

namespace App\Services\Netsuite;

use App\Models\Netsuite\NetsuiteCustomer;
use App\Models\Netsuite\NetsuiteTransactionItem;
use App\Services\KlaviyoService;
use Carbon\Carbon;



class NetsuiteCustomerService
{

    protected $klaviyoService;

    public function __construct(KlaviyoService $klaviyoService)
    {
        $this->klaviyoService = $klaviyoService;
    }
    public function trackEvents()
    {
        // $this->trackNoPurchase90Days();
        // $this->trackNoPurchase180Days();
        // $this->trackQuiltShopEmailSignedUpNoPurchase();
        // $this->trackLoriHoltPurchase();
        // $this->trackPurchaseBelow1000();
    }

    
}
