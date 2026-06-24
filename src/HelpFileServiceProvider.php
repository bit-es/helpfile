<?php

declare(strict_types=1);

namespace Bites\HelpFile;

use App\Services\BitesServiceProvider;
use Bites\HelpFile\Actions\DiscoverHelpPageMaker;

class HelpFileServiceProvider extends BitesServiceProvider
{
    protected string $configFile =
        __DIR__ . '/../config/bites.php';

    protected string $viewsPath =
        __DIR__ . '/../resources/views';

    protected string $iconsPath =
        __DIR__ . '/../resources/svg';

    public function boot(): void
    {
        parent::boot();

        app(DiscoverHelpPageMaker::class)->execute();
    }
}
