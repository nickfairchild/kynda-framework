<?php

namespace Kynda\Foundation\Bootstrap;

use Kynda\Contracts\Application;

class RegisterProviders
{
    public function bootstrap(Application $app)
    {
        $app->registerConfiguredProviders();
    }
}
