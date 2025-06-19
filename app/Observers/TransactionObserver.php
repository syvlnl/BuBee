<?php

namespace App\Observers;

use App\Models\Transaction;
use App\Models\Target;

class TransactionObserver
{
    /**
     * Handle the Transaction "created" event.
     */
    public function created(Transaction $transaction): void
    {
        if ($transaction->target_id) {
            $target = Target::find($transaction->target_id);
            if ($target) {
                $target->increment('current_amount', $transaction->amount);
                $target->save(); // trigger event saving untuk update status
            }
        }
    }

    /**
     * Handle the Transaction "updated" event.
     */
    public function updated(Transaction $transaction): void
    {
        $originalTargetId = $transaction->getOriginal('target_id');
        $originalAmount = $transaction->getOriginal('amount');

        if ($originalTargetId && !$transaction->target_id) {
            $oldTarget = Target::find($originalTargetId);
            if ($oldTarget) {
                $oldTarget->decrement('current_amount', $originalAmount);
                $oldTarget->save(); // update status
            }
        }

        if ($transaction->target_id) {
            $newTarget = Target::find($transaction->target_id);
            if (!$newTarget) return;

            if ($originalTargetId && $originalTargetId != $transaction->target_id) {
                $oldTarget = Target::find($originalTargetId);
                if ($oldTarget) {
                    $oldTarget->decrement('current_amount', $originalAmount);
                    $oldTarget->save(); // update status
                }
                $newTarget->increment('current_amount', $transaction->amount);
                $newTarget->save(); // update status
            } elseif ($originalTargetId == $transaction->target_id && $originalAmount != $transaction->amount) {
                $difference = $transaction->amount - $originalAmount;
                $newTarget->increment('current_amount', $difference);
                $newTarget->save(); // update status
            } elseif (!$originalTargetId) {
                $newTarget->increment('current_amount', $transaction->amount);
                $newTarget->save(); // update status
            }
        }
    }

    /**
     * Handle the Transaction "deleted" event.
     */
    public function deleted(Transaction $transaction): void
    {
        if ($transaction->target_id) {
            $target = Target::find($transaction->target_id);
            if ($target) {
                $target->decrement('current_amount', $transaction->amount);
                $target->save(); // update status
            }
        }
    }
}
