<?php

declare(strict_types=1);

namespace Bites\HelpFile\Actions;

use Filament\Actions\Action;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class GetHelpAction extends Action
{
    public static function make(?string $name = 'help'): static
    {
        return parent::make($name)
            ->icon('heroicon-o-book-open')
            ->iconButton()
            ->tooltip('See Guide')
            ->color('info')
            ->modalWidth('4xl')
            ->modalContent(fn ($livewire): Factory|\Illuminate\Contracts\View\View => static::getHelpView($livewire))
            ->slideOver();
    }

    protected static function getHelpView($livewire): Factory|View
    {

        [$panel, $resourceName, $pageSegment, $recordId] = static::resolveContext($livewire);

        $segments = array_filter([$resourceName, $pageSegment, $recordId]);
        $cleanPath = implode('/', $segments);

        $paths = static::buildPaths($cleanPath, $resourceName, $panel);

        foreach ($paths as $path) {
            if (File::exists($path)) {
                return view('bites::markdown', [
                    'markdown' => Str::markdown(File::get($path)),
                ]);
            }
        }

        // dd($cleanPath, $paths);
        return static::fallbackView($cleanPath, $paths);
    }

    protected static function resolveContext($livewire): array
    {
        $panel = filament()->getCurrentPanel()?->getId();

        $resourceName = null;
        if (method_exists($livewire, 'getResource')) {
            $resourceClass = class_basename($livewire::getResource());

            $resourceName = str($resourceClass)
                ->before('Resource')
                ->plural()
                ->kebab()
                ->toString();
        }

        $pageClass = class_basename($livewire);

        $recordId = null;
        if (method_exists($livewire, 'getRecord') && $livewire->getRecord()) {
            $recordId = $livewire->getRecord()->getRouteKey();
        }

        $pageSegment = match (true) {
            str_starts_with($pageClass, 'List') => null,
            str_starts_with($pageClass, 'Create') => null,
            str_starts_with($pageClass, 'Edit') => null,
            str_starts_with($pageClass, 'View') => null,
            default => str($pageClass)->kebab(),
        };

        return [$panel, $resourceName, $pageSegment, $recordId];
    }

    protected static function buildPaths(
        ?string $cleanPath,
        ?string $resourceName,
        string $panel
    ): array {
        return [
            public_path(sprintf('helpfiles/%s/%s.md', $cleanPath, $panel)),
            public_path(sprintf('helpfiles/%s/%s.md', $resourceName, $panel)),
            public_path(sprintf('helpfiles/%s.md', $panel)),
        ];
    }

    protected static function fallbackView(string $cleanPath, array $paths): Factory|View
    {
        return view('bites::markdown', [
            'markdown' => Str::markdown(
                "# No help available\n\n".
                    "**Resolved path:** `{$cleanPath}`\n\n".
                    "### Expected files:\n\n".
                    collect($paths)
                        ->map(fn (string $p): string => sprintf('- `%s`', $p))
                        ->implode("\n")
            ),
        ]);
    }
}
