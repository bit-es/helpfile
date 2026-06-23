<?php

namespace Bites\HelpFile\Actions;

use Filament\Actions\Action;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Components\ViewField;
use Illuminate\Support\Facades\Route;
use Livewire\Component;

class TopBarAction extends Component implements HasActions, HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;

    // Livewire public properties automatically persist across click events and network roundtrips
    public string $helpFile = '';
    public string $panelPath = '';

    /**
     * Runs exactly once when the component initially loads on the page.
     * This locks down the correct route data before any actions are clicked.
     */
    public function mount(): void
    {
        $route = Route::current();
        $uri = ltrim((string) $route?->uri(), '/');
        
        $this->panelPath = ltrim((string) filament()->getCurrentPanel()->getPath(), '/');

        $this->helpFile = str($uri)
            ->when($this->panelPath !== '', fn ($str) => $str->after($this->panelPath . '/'))
            ->replaceMatches('/\{tenant\}\\//', '')
            ->replaceMatches('/\}.*/', '}')
            ->replace('{record}', (string) $route?->parameter('record'))
            ->replace('/', '.')
            ->toString();
    }

    public function myHeaderAction(): Action
    {
        // Resolve the markdown file path using the frozen properties
        $markdownDirectory = public_path("helpfiles/{$this->panelPath}/");
        $targetFile = $markdownDirectory . $this->helpFile . '.md';

        if (file_exists($targetFile)) {
            $markdownContent = file_get_contents($targetFile);
        } else {
            $markdownContent = "# Documentation Not Found\nCould not find a help file for route path: `{$this->helpFile}`\n\nPlease create one at: `{$targetFile}`";
        }

        return Action::make('myHeaderAction')
            ->icon('heroicon-o-book-open')
            ->tooltip($this->helpFile . '@' . $this->panelPath) // Uses the stored state
            ->iconButton()
            ->slideOver()
            ->modalSubmitAction(false) 
            ->modalCancelActionLabel('Close') 
            ->schema([
                ViewField::make('markdown_preview')
                    ->view('bites::markdown') 
                    ->viewData([
                        'content' => $markdownContent, // Linked correctly to avoid undefined variable errors
                    ]),
            ]);
    }

    public function render()
    {
        return view('bites::help-button');
    }
}
