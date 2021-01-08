<?php

namespace Kynda\Contracts;

interface Kernel
{
    public function bootstrap();

    public function handle($request);
}
