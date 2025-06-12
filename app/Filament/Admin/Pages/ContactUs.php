<?php

namespace App\Filament\Admin\Pages;

use Filament\Actions\Action;
use Filament\Pages\Page;
use Illuminate\Contracts\View\View;

class ContactUs extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-phone';

    protected static string $view = 'filament.admin.pages.contact-us';

    protected function getViewData(): array
    {
        return [
            'whatsappLink' => 'https://wa.me/6281234567890',
        ];
    }

}
