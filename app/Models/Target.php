<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Target extends Model
{

    protected $primaryKey = 'target_id'; 
    public $incrementing = true;        
    protected $keyType = 'int';

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

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($target) {
            if ($target->amount_collected >= $target->amount_needed) {
                $target->status = 'completed';
            } else {
                $target->status = 'on progress';
            }
        });
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function getRouteKeyName()
    {
        return 'target_id';
    }

    protected $casts = [
        'deadline' => 'datetime',
    ];

}

