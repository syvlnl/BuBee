<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'category_id',
        'date_transaction',
        'amount',
        'note',
        'image' 
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function scopeExpenses($query)
    {
        return $query->whereHas('user', function ($query) {
            $query->where('id', Auth::id());
        })->whereHas('category', function ($query) {
            $query->where('is_expense', true);
        });
    }

    public static function scopeIncomes($query)
    {
        return $query->whereHas('user', function ($query) {
            $query->where('id', Auth::id());
        })->whereHas('category', function ($query) {
            $query->where('is_expense', false);
        });
    }
}
