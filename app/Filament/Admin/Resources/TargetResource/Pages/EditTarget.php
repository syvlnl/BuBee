<?php

namespace App\Filament\Admin\Resources\TargetResource\Pages;

use App\Filament\Admin\Resources\TargetResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTarget extends EditRecord
{
    protected static string $resource = TargetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
