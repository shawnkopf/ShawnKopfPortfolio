<?php

namespace App\Models\Netsuite;

use App\Models\NetsuiteShippingAddress;
use Illuminate\Database\Eloquent\Model;



class NetsuiteTransaction extends Model
{

    public function customer()
    {

        return $this->belongsTo(NetsuiteCustomer::class, 'entity_id', 'ns_customer_id');
    }

    public function shippingAddresses()
    {
        return $this->hasMany(NetsuiteShippingAddress::class, 'transaction_id', 'ns_transaction_id');
    }
}
