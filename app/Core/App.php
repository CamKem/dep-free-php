<?php

namespace App\Core;

class App extends Container
{
    protected array $registeredProviders = [];
    protected array $bootedProviders = [];

    protected array $aliases = [];
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

        array_walk($this->registeredProviders, fn($provider) =>
            $this->bootProvider($provider)
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
        // find the $provider instance in the $this->registeredProviders array
        // then return the value of the first instance found without using the collect() helper
        return array_filter(
            $this->registeredProviders,
            static fn($registeredProvider) => $registeredProvider === $provider
        )[0] ?? null;
    }

    public function alias(string $alias, string $class): void
    {
        $this->aliases[$alias] = $class;
    }

    public function hasAlias(string $alias): bool
    {
        return array_key_exists($alias, $this->aliases);
    }

    public function getAlias(string $alias): string
    {
        return $this->aliases[$alias];
    }

}

// TODO: test memory usage
// echo 'Memory usage before loading config: ' . memory_get_usage() . "\n";
// echo 'Memory usage after loading config: ' . memory_get_usage() . "\n";
// echo 'Peak memory usage: ' . memory_get_peak_usage() . "\n";
