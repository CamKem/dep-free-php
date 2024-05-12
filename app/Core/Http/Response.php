<?php

namespace App\Core\Http;

use App\Core\Routing\Router;
use App\Core\View;

class Response {
    const NOT_FOUND = 404;
    const FORBIDDEN = 403;
    const UNAUTHORIZED = 401;
    const OK = 200;

    public static function status(int $code): bool|int
    {
        return http_response_code($code);
    }

    public static function json(array $data): void
    {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    public static function body(string $body): void
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

    public static function send(): void
    {
        exit;
    }

    public function view(string $view, array $data = []): View
    {
        return view($view, $data);
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

}