<?php

namespace App\Core;

class Session
{

    protected ?string $id = null;
    protected array $data = [];

    public function __construct()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $this->id = session_id();
        $this->migrateDataForCurrentRequest();
    }

    public function get(string $key, $default = null): mixed
    {
        if (isset($this->data[$key])) {
            return $this->data[$key];
        }
        // check in the flash data
        $flashData = array_merge($this->data['_flash.old'] ?? [], $this->data['_flash.new'] ?? []);
        return $flashData[$key] ?? $default;
    }

    public function set(string $key, $value): void
    {
        $this->data[$key] = $value;
    }

    public function has(string $key): bool
    {
        if (isset($this->data[$key])) {
            return true;
        }
        $flashData = array_merge($this->data['_flash.old'] ?? [], $this->data['_flash.new'] ?? []);
        return isset($flashData[$key]);
    }

    public function remove(string $key): void
    {
        unset($this->data[$key]);
    }

    public function clear(): void
    {
        session_unset();
    }

    public function destroy(): void
    {
        session_destroy();
    }

    public function all(): array
    {
        return $this->data;
    }

    public function flash(string $key, mixed $value): void
    {
        $flashData = $this->get('_flash.new', []);
        $flashData[$key] = $value;
        $this->set('_flash.new', $flashData);
    }

    public function old(string $key, $default = null)
    {
        $old = $this->get('old', []);
        return $old[$key] ?? $default;
    }

    public function error(string $key, $default = null)
    {
        $errors = $this->get('errors', []);
        return $errors[$key] ?? $default;
    }

    protected function prepareForNextRequest(): void
    {
        // remove the current old flash data
        $this->remove('_flash.old');

        // get the new flash data
        $new = $this->get('_flash.new', []);

        // remove the _flash.new key
        $this->remove('_flash.new');

        // move the new flash data to the old flash data
        $this->set('_flash.old', $new);

        // write the data to the session
        $_SESSION = $this->data;
    }

    public function __destruct()
    {
        $this->prepareForNextRequest();
    }

    private function migrateDataForCurrentRequest(): void
    {
        $this->data = $_SESSION;
    }

}