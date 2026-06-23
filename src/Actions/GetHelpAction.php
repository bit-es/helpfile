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
        [$helpPath, $helpFile] = static::resolveContext();
        $markdown = static::getHelpView($helpPath, $helpFile);

        return parent::make($name)
            ->icon('heroicon-o-book-open')
            ->iconButton()

            ->tooltip(function (Action $action) use ($markdown) {
                $arguments = $action->getArguments();

                return sprintf(
                    // '%s at Path: %s | File: %s',
                      '%s',
                    strip_tags($markdown),
                    $arguments['helpPath'] ?? '',
                    $arguments['helpFile'] ?? '',
                );
            })

            ->color('info')
            ->action(fn() => view('bites::markdown', [
                'markdown' => $markdown,
            ]));
    }
    protected static function getHelpView(
        string $helpPath,
        string $helpFile,
    ): string {
        $path = public_path("helpfiles/{$helpPath}/{$helpFile}.md");

        if (File::exists($path)) {
            return Str::markdown(File::get($path));
        }
        // return  Str::markdown("# No help available\n\nPath: `{$path}`");
         return  Str::markdown("#`{$path}`");
    }
    protected static function resolveContext(): array
    {
        $route = request()->route();

        $helpPath = filament()->getCurrentPanel()->getPath();

        $helpFile = str($route?->uri())
            ->after($helpPath . '/')
            ->replaceMatches('/\}.*/', '}')
            ->replace('{record}', (string) $route?->parameter('record'))
            ->replace('/', '.')
            ->toString();

        return [$helpPath, $helpFile];
    }
}
