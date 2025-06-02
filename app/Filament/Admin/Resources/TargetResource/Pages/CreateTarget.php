<?php

namespace App\Filament\Admin\Resources\TargetResource\Pages;

use Illuminate\Support\Facades\Auth;
use App\Filament\Admin\Resources\TargetResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTarget extends CreateRecord
{
    protected static string $resource = TargetResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = Auth::user();
        $data['user_id'] = $user()->id(); 
        return $data;
    }
}
