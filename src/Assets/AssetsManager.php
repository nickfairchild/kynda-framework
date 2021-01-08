<?php

namespace Kynda\Assets;

use Kynda\Contracts\Application;
use Kynda\Contracts\Manifest;
use Closure;
use InvalidArgumentException;

class AssetsManager
{
    protected Application $app;

    protected array $manifests;

    protected array $customCreators = [];

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function register(string $name, Manifest $manifest): self
    {
        $this->manifests[$name] = $manifest;

        return $this;
    }

    public function manifest(string $name = null, array $config = null): Manifest
    {
        $name = $name ?: $this->getDefaultManifest();

        $manifest = $this->manifests[$name] ?? $this->resolve($name, $config);

        return $this->manifests[$name] = $manifest;
    }

    protected function resolve(string $name, ?array $config): Manifest
    {
        $config = $config ?? $this->getConfig($name);
        $strategy = $config['strategy'] ?? 'relative';

        if (isset($this->customCreators[$strategy])) {
            return $this->callCustomCreator($config);
        }

        $strategyMethod = 'create' . ucfirst($strategy) . 'Manifest';

        if (method_exists($this, $strategyMethod)) {
            return $this->{$strategyMethod}($config);
        }

        throw new InvalidArgumentException(sprintf('Strategy [%s] is not supported.', $strategy));
    }

    protected function callCustomCreator(array $config): Manifest
    {
        return $this->customCreators[$config['strategy']]($this->app, $config);
    }

    public function createRelativeManifest(array $config): RelativePathManifest
    {
        $manifest = $this->getJsonManifest($config['manifest']);

        return new RelativePathManifest($config['path'], $config['uri'], $manifest);
    }

    protected function getJsonManifest(string $jsonManifest): array
    {
        $files = $this->app->get('files');

        return $files->exists($jsonManifest) ? json_decode($files->get($jsonManifest), true) : [];
    }

    protected function getConfig(string $name): array
    {
        return $this->app->get('config')["assets.manifests.{$name}"];
    }

    public function getDefaultManifest(): string
    {
        return $this->app->get('config')['assets.default'];
    }

    public function extend(string $strategy, Closure $callback): self
    {
        $this->customCreators[$strategy] = $callback;

        return $this;
    }

    public function __call($method, $parameters)
    {
        return $this->manifest()->$method(...$parameters);
    }
}
