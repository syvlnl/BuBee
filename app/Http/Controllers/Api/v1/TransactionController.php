<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Resources\v1\TransactionCollection;
use App\Http\Resources\v1\TransactionResource;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Services\v1\TransactionQuery;

class TransactionController extends Controller
{
    public function index($user, Request $request)
    {
        $filter = new TransactionQuery();
        $queryItems = $filter->transform($request);
        $transactions = Transaction::with('user')
            ->where('user_id', $user)
            ->where($queryItems)
            ->get();
        return new TransactionCollection($transactions);
    }

    public function show(Transaction $transaction)
    {
        return new TransactionResource($transaction->load('user'));
    }
}
