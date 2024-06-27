<?php

namespace App\Models;

use App\Models\Netsuite\NetsuiteCustomer;
use App\Models\Netsuite\NetsuiteTransaction;

use Illuminate\Database\Eloquent\Model;


class NetsuiteShippingAddress extends Model
{
    protected $table = 'netsuite_addresses';
    protected $primaryKey = 'address_id';
    protected $guarded = ['id'];

    public function customer()
    {
        return $this->belongsTo(NetsuiteCustomer::class, 'customer_id', 'ns_customer_id');
    }

    public function transaction()
    {
        return $this->belongsTo(NetsuiteTransaction::class, 'transaction_id', 'ns_transaction_id');
    }
}
