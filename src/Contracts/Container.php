<?php

namespace Kynda\Contracts;

use Psr\Container\ContainerInterface;

interface Container extends ContainerInterface
{
    public function bound(string $abstract): bool;

    public function bind(string $abstract, $concrete = null, bool $shared = false): void;

    public function singleton(string $abstract, $concrete = null): void;

    public function instance(string $abstract, $instance);

    public function make(string $abstract, array $parameters = []);

    public function resolved(string $abstract): bool;
}
