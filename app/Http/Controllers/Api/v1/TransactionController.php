<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Resources\v1\TransactionCollection;
use App\Http\Resources\v1\TransactionResource;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Target;
use App\Models\Transaction;
use App\Services\v1\TransactionQuery;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;


class TransactionController extends Controller
{
    public function index($user, Request $request)
    {
        if (Auth::user()->id != $user) {
            return response()->json(['message' => 'Forbidden'], 403);
        }
        $filter = new TransactionQuery();
        $queryItems = $filter->transform($request);
        $transactions = Transaction::with(['user', 'category', 'target'])
            ->where('user_id', $user)
            ->where($queryItems)
            ->get();
        return new TransactionCollection($transactions);
    }

    public function show($user ,Transaction $transaction)
    {
        if (Auth::user()->id != $user) {
            return response()->json(['message' => 'Forbidden'], 403);
        }
        $transaction->load(['user', 'category', 'target']);
        return new TransactionResource($transaction);
    }

    public function store(Request $request, $user)
    {
        if (Auth::user()->id != $user) {
            return response()->json(['message' => 'Forbidden'], 403);
        }
        $rules = [
            'name' => 'required|string|max:50',
            'category_id' => 'required|exists:categories,id',
            'is_saving' => 'required|boolean',
            'date_transaction' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'note' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'target_id' => 'nullable|exists:targets,target_id',
        ];

        if ($request->input('is_saving')) {
            $rules['target_id'] = 'required|exists:targets,target_id';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if($request->input('is_saving')){
            $target = Target::find($request->target_id);
            if (!$target) {
                return response()->json(['message' => 'Target not found'], 404);
            }
            if ($target->user_id != $user) {
                return response()->json(['message' => 'Forbidden'], 403);
            }
            if ($target->status == 'Completed') {
                return response()->json(['message' => 'Target is already completed'], 400);
            }
            $target->amount_collected += $request->amount;
            $target->save();
        }

        $transaction = Transaction::create([
            'name' => $request->name,
            'user_id' => $user,
            'category_id' => $request->category_id,
            'target_id' => $request->target_id,
            'is_saving' => $request->is_saving,
            'date_transaction' => $request->date_transaction,
            'amount' => $request->amount,
            'note' => $request->note,
            'image' => $request->image,
        ]);

        $transaction->load(['user', 'category', 'target']);
        return new TransactionResource($transaction);
    }

    public function update(Request $request, $user, $transaction)
    {
        if (Auth::user()->id != $user) {
            return response()->json(['message' => 'Forbidden'], 403);
        }
        $transaction = Transaction::where('user_id', $user)->where('id', $transaction)->first();
        if (!$transaction) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        $rules = [
            'name' => 'sometimes|required|string|max:50',
            'category_id' => 'sometimes|required|exists:categories,id',
            'is_saving' => 'sometimes|required|boolean',
            'date_transaction' => 'sometimes|required|date',
            'amount' => 'sometimes|required|numeric|min:0',
            'note' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'target_id' => 'nullable|exists:targets,target_id',
        ];
        
        $isSavingNow = $request->has('is_saving') ? $request->input('is_saving') : $transaction->is_saving;
        $wasSaving = $transaction->is_saving;
        
        if ($isSavingNow && !$wasSaving) {
            $rules['target_id'] = 'required|exists:targets,target_id';
        } elseif (!$isSavingNow && $wasSaving) {
            $rules['target_id'] = 'nullable|exists:targets,target_id';
        }

        $validator = Validator::make($request->all(), $rules);

        // Check if 'is_saving' is being updated

        // If changing from not saving to saving, add amount to target
        if ($isSavingNow && !$wasSaving) {
            $target = Target::find($request->target_id);
            if (!$target) {
            return response()->json(['message' => 'Target not found'], 404);
            }
            if ($target->user_id != $user) {
            return response()->json(['message' => 'Forbidden'], 403);
            }
            if ($target->status == 'Completed') {
            return response()->json(['message' => 'Target is already completed'], 400);
            }
            // Use the provided amount if present, otherwise use the transaction's current amount
            $amountToAdd = $request->has('amount') ? $request->input('amount') : $transaction->amount;
            $target->amount_collected += $amountToAdd;
            $target->save();
        }

        // If changing from saving to not saving, subtract amount from target
        if (!$isSavingNow && $wasSaving) {
            $target = Target::find($transaction->target_id);
            if ($target) {
                $target->amount_collected -= $transaction->amount;
                if ($target->amount_collected < 0) {
                    $target->amount_collected = 0;
                }
                $target->save();
            }
            $request->merge(['target_id' => null]);
        }

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $dataToUpdate = $request->only(array_keys($rules));
        // Ensure amount is always set for update logic
        if (!$request->has('amount') && in_array('amount', array_keys($rules))) {
            $dataToUpdate['amount'] = $transaction->amount;
        }
        if ($request->has('is_saving') && !$isSavingNow) {
            $dataToUpdate['target_id'] = null;
        }
        $transaction->update($dataToUpdate);

        $transaction->load(['user', 'category', 'target']);
        return new TransactionResource($transaction);
    }
    
    public function destroy($user, $transaction)
    {
        if (Auth::user()->id != $user) {
            return response()->json(['message' => 'Forbidden'], 403);
        }
        $transaction = Transaction::where('user_id', $user)->where('id', $transaction)->first();
        if (!$transaction) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }
        $transaction->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }
}
