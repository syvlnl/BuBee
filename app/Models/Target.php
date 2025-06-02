<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Target extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'amount_needed',
        'amount_collected',
        'deadline',
        'status'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopeUserTargets($query)
    {
        return $query->where('user_id', auth()->id());
    }
}
