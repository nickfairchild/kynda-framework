<?php

namespace Kynda\Support;

use Kynda\Contracts\Container;
use Closure;

abstract class ServiceProvider
{
    protected Container $app;

    protected array $bootingCallbacks = [];

    protected array $bootedCallbacks = [];

    public function __construct(Container $app)
    {
        $this->app = $app;
    }

    public function register(): void
    {
        //
    }

    public function booting(Closure $callback): void
    {
        $this->bootingCallbacks[] = $callback;
    }

    public function booted(Closure $callback): void
    {
        $this->bootedCallbacks[] = $callback;
    }

    public function callBootingCallbacks(): void
    {
        foreach ($this->bootingCallbacks as $callback) {
            $this->app->call($callback);
        }
    }

    public function callBootedCallbacks(): void
    {
        foreach ($this->bootedCallbacks as $callback) {
            $this->app->call($callback);
        }
    }

    public function provides(): array
    {
        return [];
    }
}
