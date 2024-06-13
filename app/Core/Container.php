<?php

namespace App\Core;

use RuntimeException;

class Container
{
    protected static self $instance;
    protected array $bindings = [];

    public function bind($key, $resolver, $singleton = false): void
    {
        $this->bindings[$key] = compact('resolver', 'singleton');
    }

    public function singleton(string $class, $args = null): void
    {
        $this->bind($class, static fn() => new $class($args), true);
    }

    /**
     * Resolves a class from the container
     *
     * @param string $key (The key of the binding to resolve)
     * @return object (The resolved instance of the binding)
     * @throws RuntimeException (If no matching binding is found)
     */
    public function resolve(string $key): mixed
    {
        if (!isset($this->bindings[$key])) {

            throw new RuntimeException("No matching binding found for {$key}");
        }

        $binding = $this->bindings[$key];

        if ($binding['singleton'] && isset($binding['instance'])) {
            return $binding['instance'];
        }

        $resolver = $binding['resolver'];

        if (!is_callable($resolver)) {
            if (!class_exists($resolver)) {
                throw new RuntimeException("Resolver for {$key} is not a valid class.");
            }
            throw new RuntimeException("Resolver for {$key} is not a valid callable.");
        }

        $instance = $resolver();

        if ($binding['singleton']) {
            $this->bindings[$key]['instance'] = $instance;
        }

        return $instance;
    }


    public function unBind($key): void
    {
        if (array_key_exists($key, $this->bindings)) {
            unset($this->bindings[$key]);
        }
    }

    public static function getContainer(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public static function setContainer(self $container): self
    {
        return self::$instance = $container;
    }

    public function isSingleton(string $key): bool
    {
        return $this->bindings[$key]['singleton'] ?? false;
    }

    public function isBound(string $key): bool
    {
        return array_key_exists($key, $this->bindings);
    }


}