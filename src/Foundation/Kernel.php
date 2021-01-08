<?php

namespace Kynda\Foundation;

use Kynda\Contracts\Application;
use Kynda\Foundation\Bootstrap\BootProviders;
use Kynda\Foundation\Bootstrap\LoadConfiguration;
use Kynda\Foundation\Bootstrap\RegisterProviders;

class Kernel
{
    protected $app;

    protected $bootstrappers = [
        LoadConfiguration::class,
        RegisterProviders::class,
        BootProviders::class
    ];

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function bootstrap()
    {
        if (! $this->app->hasBeenBootstrapped()) {
            $this->app->bootstrapWith($this->bootstrappers());
        }
    }

    protected function bootstrappers()
    {
        return $this->bootstrappers;
    }

    public function getApplication()
    {
        return $this->app;
    }
}
