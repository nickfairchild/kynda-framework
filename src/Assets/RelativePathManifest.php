<?php

namespace Kynda\Assets;

use Kynda\Contracts\Asset as AssetContract;
use Kynda\Contracts\Manifest;
use ArrayAccess;
use ArrayIterator;
use Countable;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use IteratorAggregate;
use JsonSerializable;

class RelativePathManifest implements
    Arrayable,
    ArrayAccess,
    Countable,
    IteratorAggregate,
    Jsonable,
    JsonSerializable,
    Manifest
{
    protected array $manifest = [];

    protected string $path;

    protected string $uri;

    public function __construct(string $path, string $uri, array $manifest)
    {
        $this->path = $path;
        $this->uri = $uri;

        $manifest = $manifest instanceof Arrayable ? $manifest->toArray() : (array) $manifest;

        foreach ($manifest as $key => $value) {
            $this->set($key, $value);
        }
    }

    public function set($original, $revved): void
    {
        $this->manifest[$this->normalizeRelativePath($original)] = $this->normalizeRelativePath($revved);
    }

    public function get($key): AssetContract
    {
        $key = $this->normalizeRelativePath($key);
        $relative_path = $this->manifest[$key] ?? $key;

        return new Asset("{$this->path}/{$relative_path}", "{$this->uri}/{$relative_path}");
    }

    protected function normalizeRelativePath(string $path): string
    {
        $path = str_replace('\\', '/', $path);

        return ltrim($path, '/');
    }

    public function offsetExists($key): bool
    {
        return array_key_exists($key, $this->manifest);
    }

    public function offsetGet($key): AssetContract
    {
        return $this->get($key);
    }

    public function offsetSet($key, $value): void
    {
        $this->set($key, $value);
    }

    public function offsetUnset($key): void
    {
        unset($this->manifest[$key]);
    }

    public function count(): int
    {
        return count($this->manifest);
    }

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->manifest);
    }

    public function jsonSerialize()
    {
        return $this->manifest;
    }

    public function toJson($options = 0): string
    {
        return json_encode($this->jsonSerialize(), $options);
    }

    public function toArray(): array
    {
        return $this->manifest;
    }

    public function uri(): string
    {
        return $this->uri;
    }

    public function path(): string
    {
        return $this->path;
    }
}
