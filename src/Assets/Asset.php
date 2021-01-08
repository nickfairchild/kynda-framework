<?php

namespace Kynda\Assets;

use Kynda\Contracts\Asset as AssetContract;
use Illuminate\Support\Str;

class Asset implements AssetContract
{
    protected string $path;

    protected string $uri;

    public function __construct(string $path, string $uri)
    {
        $this->path = Str::before($path, '?');
        $this->uri = $uri;
    }

    public function uri(): string
    {
        return $this->uri;
    }

    public function path(): string
    {
        return $this->path;
    }

    public function exists(): bool
    {
        return file_exists($this->path());
    }

    public function contents(): string
    {
        if (! $this->exists()) {
            return false;
        }

        return file_get_contents($this->path());
    }

    public function get()
    {
        if (! $this->exists()) {
            return false;
        }

        return include $this->path();
    }

    public function __toString(): string
    {
        return $this->uri();
    }
}
