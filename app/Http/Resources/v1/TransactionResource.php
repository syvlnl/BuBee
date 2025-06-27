<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'userId' => $this->user_id,
            'categoryId' => $this->category_id,
            'targetId' => $this->target_id,
            'isSaving' => $this->is_saving,
            'dateTransaction' => $this->date_transaction,
            'amount' => $this->amount,
            'note' => $this->note,
            'image' => $this->image,
            'categoryName' => $this->whenLoaded('category') ? $this->category->name : null,
            'categoryIsExpense' => $this->whenLoaded('category') ? $this->category->is_expense : null,
            'targetName' => $this->whenLoaded('target') ? $this->target->name : null,
        ];
    }
}
