<?php

namespace App\Models\Netsuite;

use App\Models\NetsuiteShippingAddress;
use Illuminate\Database\Eloquent\Model;



class NetsuiteCustomer extends Model
{
    protected $guarded = ['id'];

    public function transactions()
    {
        return $this->hasMany(NetsuiteTransaction::class, 'entity_id', 'ns_customer_id');
    }

    public function shippingAddresses()
    {
        return $this->hasMany(NetsuiteShippingAddress::class, 'customer_id', 'ns_customer_id');
    }
}
