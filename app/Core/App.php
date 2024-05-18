<?php

namespace App\Core;

use http\Exception\RuntimeException;
use InvalidArgumentException;
use ReflectionClass;
use ReflectionNamedType;

class App extends Container
{
    protected array $registeredProviders = [];
    protected array $bootedProviders = [];
    protected bool $booted = false;

    public function __construct()
    {
        static::setContainer($this);
    }

    public function boot(): true
    {
        if ($this->isBooted()) {
            return true;
        }

        array_walk($this->registeredProviders, fn($provider) => $this->bootProvider($provider)
        );

        return $this->booted = true;
    }

    // TODO: test this unBoot method to see if it works
    public function unBoot(): void
    {
        $this->booted = false;

        // dissolve the resolved providers
        foreach ($this->bootedProviders as $provider) {
            $this->unBootProvider($provider);
        }
        // remove them from the container, by unbinding them
        array_walk($this->registeredProviders, fn($provider) => $this->unBind($provider));
    }

    public function isBooted(): bool
    {
        return $this->booted;
    }

    public function debugInfo(): array
    {
        return [
            'services' => [
                'registered' => array_map(get_class(...), $this->registeredProviders),
                'booted' => array_map(get_class(...), $this->bootedProviders),
            ],
            'isBooted' => $this->booted,
        ];
    }

    public function registerProvider(ServiceProvider $provider): ServiceProvider
    {
        if ($registered = $this->getProvider($provider)) {
            return $registered;
        }

        $this->registeredProviders[] = $provider;
        $provider->register();

        if ($this->isBooted()) {
            $this->bootProvider($provider);
        }

        return $provider;
    }

    protected function bootProvider(ServiceProvider $provider): void
    {
        foreach ($this->registeredProviders as $registeredProvider) {
            if (($registeredProvider === $provider) && !in_array($provider, $this->bootedProviders, true)) {
                $provider->boot();
                $this->bootedProviders[] = $provider;
            }
        }
    }

    protected function unBootProvider(ServiceProvider $provider): void
    {
        // TODO: set up to remove the resolved providers from the container
        //  also remove them from the bootedProviders array
        if (method_exists($provider, 'unBoot')) {
            $provider->unBoot();
            // remove the provider from the bootedProviders array
            $this->bootedProviders = array_filter(
                $this->bootedProviders,
                static fn($bootedProvider) => $bootedProvider !== $provider
            );
        }
    }

    public function getProvider(ServiceProvider $provider): ServiceProvider|null
    {
        return array_filter(
            $this->registeredProviders,
            static fn($registeredProvider) => $registeredProvider === $provider
        )[0] ?? null;
    }

    public function alias(string $alias, string $class): void
    {
        if ($this->has($alias)) {
            throw new InvalidArgumentException(
                "Alias {$alias} is already in use"
            );
        }
        // to allow for dependency injection, we need to bind the alias to the class
        $this->bind($alias, function () use ($class) {
            // get the class constructor
            $constructor = (new ReflectionClass($class))->getConstructor();

            if ($constructor === null) {
                // TODO: set up so that DI is handled for all classes and methods
                //  not just in aliases but all bindings and resolutions
                //  from the container, for now, we will just return a new instance of the class
                return new $class;
            }

            // get the parameters of the constructor
            $parameters = $constructor->getParameters();

            // resolve each parameter from the container
            $dependencies = array_map(static function ($parameter) use ($class) {
                $type = $parameter->getType();
                if (!$type instanceof ReflectionNamedType || $type->isBuiltin()) {
                    throw new RuntimeException(
                        "Cannot resolve parameter \${$parameter->getName()} in {$class}"
                    );
                }
                return static::getContainer()->resolve($type->getName());
            }, $parameters);

            return new $class(...$dependencies);
        });
    }

    public function has(string $alias): bool
    {
        return $this->isBound($alias);
    }

}

// NOTE: use these for memory usage testing
//echo 'Memory usage now: ' . round((memory_get_usage() / 1024), 2) . "KB \n";
//echo 'Peak memory usage: ' . round((memory_get_peak_usage() / 1024), 2) . " KB\n";