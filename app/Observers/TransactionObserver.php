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
        if ($transaction->is_saving && $transaction->target_id) {
            $target = Target::find($transaction->target_id);
            if ($target) {
                $target->amount_collected += $transaction->amount;
                $target->save();
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
        $originalIsSaving = $transaction->getOriginal('is_saving');
        
        // If changed from saving to not saving
        if ($originalIsSaving && !$transaction->is_saving && $originalTargetId) {
            $oldTarget = Target::find($originalTargetId);
            if ($oldTarget) {
                $oldTarget->amount_collected -= $originalAmount;
                if ($oldTarget->amount_collected < 0) {
                    $oldTarget->amount_collected = 0;
                }
                $oldTarget->save();
            }
        }
        
        // If changed from not saving to saving
        if (!$originalIsSaving && $transaction->is_saving && $transaction->target_id) {
            $newTarget = Target::find($transaction->target_id);
            if ($newTarget) {
                $newTarget->amount_collected += $transaction->amount;
                $newTarget->save();
            }
        }
        
        // If both old and new are saving transactions
        if ($originalIsSaving && $transaction->is_saving) {
            // If target changed
            if ($originalTargetId && $originalTargetId != $transaction->target_id) {
                $oldTarget = Target::find($originalTargetId);
                if ($oldTarget) {
                    $oldTarget->amount_collected -= $originalAmount;
                    if ($oldTarget->amount_collected < 0) {
                        $oldTarget->amount_collected = 0;
                    }
                    $oldTarget->save();
                }
                
                if ($transaction->target_id) {
                    $newTarget = Target::find($transaction->target_id);
                    if ($newTarget) {
                        $newTarget->amount_collected += $transaction->amount;
                        $newTarget->save();
                    }
                }
            } 
            // If same target but amount changed
            elseif ($originalTargetId == $transaction->target_id && $originalAmount != $transaction->amount) {
                $target = Target::find($transaction->target_id);
                if ($target) {
                    $difference = $transaction->amount - $originalAmount;
                    $target->amount_collected += $difference;
                    if ($target->amount_collected < 0) {
                        $target->amount_collected = 0;
                    }
                    $target->save();
                }
            }
            // If no original target but now has target
            elseif (!$originalTargetId && $transaction->target_id) {
                $newTarget = Target::find($transaction->target_id);
                if ($newTarget) {
                    $newTarget->amount_collected += $transaction->amount;
                    $newTarget->save();
                }
            }
        }
    }

    /**
     * Handle the Transaction "deleted" event.
     */
    public function deleted(Transaction $transaction): void
    {
        if ($transaction->is_saving && $transaction->target_id) {
            $target = Target::find($transaction->target_id);
            if ($target) {
                $target->amount_collected -= $transaction->amount;
                if ($target->amount_collected < 0) {
                    $target->amount_collected = 0;
                }
                $target->save();
            }
        }
    }
}