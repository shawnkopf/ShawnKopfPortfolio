<?php

namespace App\Models\Netsuite;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NetsuiteTransactionItem extends Model
{

    use HasFactory;

    protected static function newFactory()
    {

        return \Database\Factories\NetsuiteTransactionItemFactory::new();
    }

    public function transaction()
    {
        return $this->belongsTo(NetsuiteTransaction::class, 'ns_transaction_id');
    }
}
