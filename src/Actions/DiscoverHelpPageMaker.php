<?php

declare(strict_types=1);

namespace Bites\HelpFile\Actions;

use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Bites\HelpFile\Enums\Language;
use Livewire\Livewire;
use Bites\HelpFile\Actions\GetHelpAction;

class DiscoverHelpPageMaker
{
    public function execute(): void
    {
        Livewire::component('bites.help-button', GetHelpAction::class);
 
        FilamentView::registerRenderHook(
            PanelsRenderHook::USER_MENU_AFTER,
            fn(): string => \Illuminate\Support\Facades\Blade::render('@livewire(\'bites.help-button\')'),
        );
    }
}
