<?php

namespace App\Filament\Admin\Resources\TransactionResource\Pages;

use Illuminate\Support\Facades\Auth;
use App\Filament\Admin\Resources\TransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTransaction extends CreateRecord
{
    protected static string $resource = TransactionResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return [...$data, 'user_id' => Auth::id()];
    }
}
