<?php

namespace App\Core\Http;

use App\Core\Routing\Router;
use App\Core\Template;
use InvalidArgumentException;
use JsonException;

class Response {
    const int NOT_FOUND = 404;
    const int FORBIDDEN = 403;
    const int UNAUTHORIZED = 401;
    const int OK = 200;
    const int INTERNAL_SERVER_ERROR = 500;

    public function status(int $code): self
    {
        http_response_code($code);
        return new self;
    }

    /** @throws JsonException */
    public function json(array $data): self
    {
        header('Content-Type: application/json');
        echo json_encode($data, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT);
        return new self;
    }

    public function body(string $body): void
    {
        echo $body;
        exit;
    }

    public function withInput(array $input): static
    {
        session()->flash('old', $input);
        return $this;
    }

    public function withErrors(array $errors): static
    {
        session()->flash('errors', $errors);
        return $this;
    }

    public function view(string $view, array $data = []): Template
    {
        return view($view, $data);
    }

    public function back(): static
    {
        return self::redirect(
            session()->get('previous.url', '/')
        );
    }

    public static function redirect(string $url): static
    {
        header('Location: ' . $url);
        return new static;
    }

    // method for redirecting to a named route
    public static function route(string $name, array $params = []): static
    {
        return self::redirect(app(Router::class)->generate($name, $params));
    }

    // flash a session with a key and value, or an array of key value pairs
    public function with(string|array $key, mixed $value = null): static
    {
        if (is_array($key)) {
            foreach ($key as $sessionKey => $sessionValue) {
                session()->flash($sessionKey, $sessionValue);
            }
        } else {
            session()->flash($key, $value);
        }
        return $this;
    }

    public function file(string $path, ?string $name = null): void
    {
        if (!file_exists($path)) {
            throw new InvalidArgumentException('File not found.');
        }
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . ($name ?? basename($path)) . '"');
        readfile($path);
        exit;
    }

}