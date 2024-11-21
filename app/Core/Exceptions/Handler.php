<?php

namespace App\Core\Exceptions;

use App\Core\Http\Response;
use App\Core\Template;
use JsonException;
use Throwable;

class Handler
{

    public function handle(Throwable $e): Response|Template
    {
        error_log($e->getMessage());
        if ($e instanceof RouteException) {
            redirect()->status($e->getCode())
                ->view('errors.exception', [
                    'title' => 'Route Not Found',
                    'message' => $e->getMessage()
                ]);
        } elseif ($e instanceof ValidationException) {
            redirect()->back()
                ->withInput($e->old())
                ->withErrors($e->errors());
        } elseif ($e instanceof JsonException || request()->wantsJson()) {
            return response()->json([
                'error' => $e->getMessage()
            ]);
        }
        return redirect()
            ->status($e->getCode())
            ->view('errors.exception', [
            'title' => 'Exception',
            'message' => $e->getMessage()
        ]);
    }

}