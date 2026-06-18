<?php

declare(strict_types=1);

namespace Bites\HelpFile;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\File;

class HelpFileServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'bites');

        $source = __DIR__ . '/../resources/files';
        $target = public_path('files');

        File::ensureDirectoryExists($target);

        foreach (File::allFiles($source) as $file) {

            $relativePath = $file->getRelativePathname();
            $destination = $target . DIRECTORY_SEPARATOR . $relativePath;

            File::ensureDirectoryExists(dirname($destination));

            if (! File::exists($destination)) {
                File::copy($file->getPathname(), $destination);
            }
        }
    }
    public function register(): void
    {
        //
    }
}
