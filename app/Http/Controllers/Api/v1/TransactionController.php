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
            'categoryId' => 'required|exists:categories,id',
            'isSaving' => 'required|boolean',
            'dateTransaction' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'note' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'targetId' => 'nullable|exists:targets,target_id',
        ];

        if ($request->input('isSaving')) {
            $rules['targetId'] = 'required|exists:targets,target_id';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if($request->input('isSaving')){
            $target = Target::find($request->targetId);
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
            'category_id' => $request->categoryId,
            'target_id' => $request->targetId,
            'is_saving' => $request->isSaving,
            'date_transaction' => $request->dateTransaction,
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
            'categoryId' => 'sometimes|required|exists:categories,id',
            'isSaving' => 'sometimes|required|boolean',
            'dateTransaction' => 'sometimes|required|date',
            'amount' => 'sometimes|required|numeric|min:0',
            'note' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'targetId' => 'nullable|exists:targets,target_id',
        ];
        
        $isSavingNow = $request->has('isSaving') ? $request->input('isSaving') : $transaction->is_saving;
        $wasSaving = $transaction->is_saving;
        
        if ($isSavingNow) {
            $rules['targetId'] = 'required|exists:targets,target_id';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Check if 'is_saving' is being updated

        // If changing from not saving to saving, add amount to target
        if ($isSavingNow && !$wasSaving) {
            $target = Target::find($request->targetId);
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
        }

        // If staying as saving but changing amount or target, update target calculations
        if ($isSavingNow && $wasSaving) {
            $oldAmount = $transaction->amount;
            $newAmount = $request->has('amount') ? $request->input('amount') : $transaction->amount;
            $oldTargetId = $transaction->target_id;
            $newTargetId = $request->has('targetId') ? $request->input('targetId') : $transaction->target_id;

            // If changing target
            if ($oldTargetId != $newTargetId) {
                // Remove from old target
                if ($oldTargetId) {
                    $oldTarget = Target::find($oldTargetId);
                    if ($oldTarget) {
                        $oldTarget->amount_collected -= $oldAmount;
                        if ($oldTarget->amount_collected < 0) {
                            $oldTarget->amount_collected = 0;
                        }
                        $oldTarget->save();
                    }
                }
                
                // Add to new target
                if ($newTargetId) {
                    $newTarget = Target::find($newTargetId);
                    if ($newTarget) {
                        if ($newTarget->user_id != $user) {
                            return response()->json(['message' => 'Forbidden'], 403);
                        }
                        if ($newTarget->status == 'Completed') {
                            return response()->json(['message' => 'Target is already completed'], 400);
                        }
                        $newTarget->amount_collected += $newAmount;
                        $newTarget->save();
                    }
                }
            } 
            // If same target but different amount
            elseif ($oldAmount != $newAmount && $oldTargetId) {
                $target = Target::find($oldTargetId);
                if ($target) {
                    $target->amount_collected = $target->amount_collected - $oldAmount + $newAmount;
                    if ($target->amount_collected < 0) {
                        $target->amount_collected = 0;
                    }
                    $target->save();
                }
            }
        }

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Prepare data for update with proper field mapping
        $dataToUpdate = [];
        
        if ($request->has('name')) {
            $dataToUpdate['name'] = $request->input('name');
        }
        if ($request->has('categoryId')) {
            $dataToUpdate['category_id'] = $request->input('categoryId');
        }
        if ($request->has('isSaving')) {
            $dataToUpdate['is_saving'] = $request->input('isSaving');
        }
        if ($request->has('dateTransaction')) {
            $dataToUpdate['date_transaction'] = $request->input('dateTransaction');
        }
        if ($request->has('amount')) {
            $dataToUpdate['amount'] = $request->input('amount');
        }
        if ($request->has('note')) {
            $dataToUpdate['note'] = $request->input('note');
        }
        if ($request->has('image')) {
            $dataToUpdate['image'] = $request->input('image');
        }
        
        // Handle target_id based on is_saving status
        if (!$isSavingNow) {
            $dataToUpdate['target_id'] = null;
        } elseif ($request->has('targetId')) {
            $dataToUpdate['target_id'] = $request->input('targetId');
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

    public function getIncome($user, Request $request)
    {
        if (Auth::user()->id != $user) {
            return response()->json(['message' => 'Forbidden'], 403);
        }
        $filter = new TransactionQuery();
        $queryItems = $filter->transform($request);
        $income = Transaction::where('user_id', $user)
            ->where('is_saving', false)
            ->where('amount', '>', 0)
            ->whereMonth('date_transaction', now()->month)
            ->whereYear('date_transaction', now()->year)
            ->where($queryItems)
            ->whereHas('category', function ($query) {
            $query->where('is_expense', false);
            })
            ->sum('amount');
        return response()->json(['income' => $income]);
    }

    public function getExpense($user, Request $request)
    {
        if (Auth::user()->id != $user) {
            return response()->json(['message' => 'Forbidden'], 403);
        }
        $filter = new TransactionQuery();
        $queryItems = $filter->transform($request);
        $expense = Transaction::where('user_id', $user)
            ->where('is_saving', false)
            ->where('amount', '>', 0)
            ->whereMonth('date_transaction', now()->month)
            ->whereYear('date_transaction', now()->year)
            ->where($queryItems)
            ->whereHas('category', function ($query) {
                $query->where('is_expense', true);
            })
            ->sum('amount');
        return response()->json(['expense' => $expense]);
    }
}
