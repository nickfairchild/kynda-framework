<?php

namespace Kynda\Providers;

use Kynda\Support\ServiceProvider;
use Illuminate\Filesystem\Filesystem;

class FilesystemServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton('files', function () {
            return new Filesystem;
        });
    }
}
