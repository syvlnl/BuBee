<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Resources\v1\TransactionCollection;
use App\Http\Resources\v1\TransactionResource;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Transaction;

class TransactionController extends Controller
{
    public function index()
    {
        return new TransactionCollection(Transaction::with('user')->get());
    }

    public function show(Transaction $transaction)
    {
        return new TransactionResource($transaction->load('user'));
    }
}
