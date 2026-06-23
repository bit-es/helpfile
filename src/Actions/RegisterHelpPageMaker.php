<?php

declare(strict_types=1);

namespace Bites\HelpFile\Actions;

use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Bites\HelpFile\Enums\Language;
use Livewire\Livewire;
use Bites\HelpFile\Actions\TopBarAction;

class RegisterHelpPageMaker
{
    public function execute(): void
    {
        Livewire::component('bites.help-button', TopBarAction::class);
        // Livewire::component('bites::help-button', TopBarAction::class);
        FilamentView::registerRenderHook(
            PanelsRenderHook::USER_MENU_BEFORE,
            // fn(): string => \Illuminate\Support\Facades\Blade::render('@livewire(\'bites::help-button\')'),
            fn(): string => \Illuminate\Support\Facades\Blade::render('@livewire(\'bites.help-button\')'),
        );
    }
}
