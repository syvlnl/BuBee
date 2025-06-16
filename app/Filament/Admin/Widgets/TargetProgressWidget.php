<?php

namespace App\Filament\Admin\Widgets;

use App\Filament\Admin\Resources\TargetResource;
use App\Models\Target;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class TargetProgressWidget extends Widget
{
    protected static string $view = 'filament.widgets.target-progress-bar-widget';
    protected int | string | array $columnSpan = 'full';

    public ?Target $target;
    public int $percentage = 0;

    public function mount(): void
    {
        $this->target = Target::where('user_id', Auth::id())->first();

        if ($this->target && $this->target->amount_needed > 0) {
            $this->percentage = round(($this->target->amount_collected / $this->target->amount_needed) * 100);
        }
    }

    public function getTargets(): string
    {
        return TargetResource::getUrl('index');
    }
}