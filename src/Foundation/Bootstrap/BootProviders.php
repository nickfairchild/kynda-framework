<?php

namespace Kynda\Foundation\Bootstrap;

use Kynda\Contracts\Application;

class BootProviders
{
    public function bootstrap(Application $app)
    {
        $app->boot();
    }
}
