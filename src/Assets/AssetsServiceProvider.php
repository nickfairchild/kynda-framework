<?php

namespace Kynda\Assets;

use Kynda\Contracts\Application;
use Kynda\Support\ServiceProvider;

class AssetsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton('assets', function (Application $app) {
            return new AssetsManager($app);
        });

        $this->app->singleton('assets.manifest', function () {
            return $this->app->get('assets')->manifest();
        });
    }
}
