<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class QuiltUpdate extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $with = ['user'];

    public function quilt(): BelongsTo
    {
        return $this->belongsTo(Quilt::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
