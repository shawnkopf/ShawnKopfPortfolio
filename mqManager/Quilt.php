<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Log;

class Quilt extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    protected $with = ['quiltUpdates', 'status'];

    protected $appends = ['dueDate', 'isDangerZone', 'isPosOrder', 'orderName', 'customerName', 'orderNumber'];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'shopify_order_id', 'shopify_id');
    }

    public function quiltUpdates()
    {
        return $this->hasMany(QuiltUpdate::class)->orderBy('created_at', 'desc');
    }

    public function status(): HasOne
    {
        return $this->hasOne(QuiltUpdate::class)->latestOfMany();
    }

    public function scopeOfStatus($query, $status): mixed
    {
        if ($status == '') {
            return $query;
        }

        if ($status == 'queue') {
            return $query->whereHas('status', function ($query) {
                return $query->whereNot('status', 'shipped');
            });
        }

        return $query->whereHas('status', function ($query) use ($status) {
            return $query->where('status', $status);
        });
    }

    public function getDueDateAttribute()
    {
        $receivedDate = $this->received;

        if (!$receivedDate) {
            return null;
        }

        $dueDate = date('Y-m-d', strToTime($receivedDate .  ' + 14 days'));

        if ($this->expedited) {
            $dueDate = date('Y-m-d', strToTime($receivedDate .  ' + 7 days'));
        }

        if ($this->has_binding) {
            $dueDate = date('Y-m-d', strtotime($dueDate . '- 1 days'));
        }

        return $dueDate;
    }

    public function getIsDangerZoneAttribute()
    {
        $dangerZoneDays = config('app.dangerZoneDays');
        if (!$this->dueDate) {
            return false;
        }
        $today = date('Y-m-d');
        $daysLeft = (strtotime($this->dueDate) - strToTime($today)) / 60 / 60 / 24;
        if ($daysLeft <= $dangerZoneDays) {
            return true;
        }

        return false;
    }

    public function getOrderNameAttribute()
    {
        return $this->order->name;
    }

    public function getCustomerNameAttribute()
    {
        $name = $this->order->first_name . " " . $this->order->last_name;
        return $name;
    }

    public function getReceivedDateAttribute()
    {
        $receivedUpdate = $this->quiltUpdates()->where('status', 'received')->first();
        if (!$receivedUpdate) {
            return null;
        }

        return $receivedUpdate->update_date;
    }

    public function getIsPosOrderAttribute()
    {
        if ($this->order->source_name === 'pos') {
            return true;
        }
        return false;
    }

    public function getOrderNumberAttribute()
    {
        Log::info('Accessing shopify_order_id:', ['attributes' => $this->attributes]);
        return (string) $this->attributes['shopify_order_id'];
    }
}
