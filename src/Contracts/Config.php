<?php

namespace Kynda\Contracts;

interface Config
{
    public function has(string $key): bool;

    public function get($key, $default = null);

    public function all(): array;

    public function set($key, $value = null): void;

    public function prepend(string $key, $value): void;

    public function push(string $key, $value): void;
}
