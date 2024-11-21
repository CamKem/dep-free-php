<?php

namespace App\Core\Http;

use CurlHandle;
use InvalidArgumentException;
use JsonException;
use RuntimeException;
use SensitiveParameter;

/**
 * Class External - used to make all external HTTP requests
 * We will link this to the @see HttpService
 *
 * @method External get(string $url)
 * @method External post(string $url)
 * @method External put(string $url)
 * @method External patch(string $url)
 * @method External delete(string $url)
 * @method External head(string $url)
 * @method External options(string $url)
 * @method External trace(string $url)
 *
 * @method array|string response(?string $key = null)
 * @method int status()
 * @method array headers()
 * @method array body()
 */
class External
{

    protected array $headers = [];
    protected array $body = [];
    protected array $options = [];
    protected array $response = [];
    protected int $status = 0;
    protected CurlHandle|false $ch;

    public function __construct(protected string $url, protected string $method = 'GET')
    {
        $this->ch = curl_init();

        $this->options = [
            CURLOPT_URL => $this->url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_SSL_VERIFYPEER => true,
        ];
    }

    public static function __callStatic(string $name, array $arguments): self
    {
        $method = strtoupper($name);
        if (
            in_array($method, ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'])
            && filter_var($arguments[0], FILTER_VALIDATE_URL)
        ) {
        return new self($arguments[0], $method);
        }
        throw new InvalidArgumentException('Invalid method or URL');
    }

    public function __call(string $name, array $arguments): mixed
    {
        if (property_exists($this, $name)) {
            if ($name === 'response' && !empty($arguments) && is_string($arguments[0]) && array_key_exists($arguments[0], $this->response)) {
                return $this->response[$arguments[0]];
            }
            return $this->$name;
        }
        return null;
    }

    public function wantsJson(): self
    {
        $this->headers['Content-Type'] = 'application/json';
        $this->headers['Accept'] = 'application/json';
        return $this;
    }

    public function withOptions(array $options): self
    {
        $this->options = array_replace($this->options, $options);
        return $this;
    }

    public function withHeaders(#[SensitiveParameter] array $headers): self
    {
        $this->headers = array_replace($this->headers, $headers);
        return $this;
    }

    public function withBody(array $body): self
    {
        $this->body = $body;
        return $this;
    }

    /**
     * @throws JsonException
     */
    public function send(): self
    {
        curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, $this->method);
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, array_map(
            static fn($key, $value) => "{$key}: {$value}",
            array_keys($this->headers),
            $this->headers
        ));
        if (!empty($this->body)) {
            curl_setopt($this->ch, CURLOPT_POSTFIELDS, json_encode($this->body, JSON_THROW_ON_ERROR));
        }
        curl_setopt_array($this->ch, $this->options);

        $response = curl_exec($this->ch);

        if ($response === false) {
            throw new RuntimeException('cURL error: ' . curl_error($this->ch));
        }

        $this->status = curl_getinfo($this->ch, CURLINFO_HTTP_CODE);
        $contentType = curl_getinfo($this->ch, CURLINFO_CONTENT_TYPE);

        $jsonResponseTypes = [
            'application/json',
            'application/vnd.api+json',
            'application/hal+json',
            'application/problem+json',
        ];

        if (str_contains($contentType, ';')) {
            $contentType = explode(';', $contentType)[0];
        }

        if (in_array($contentType, $jsonResponseTypes, true)) {
            $decodedResponse = json_decode($response, true, 512, JSON_THROW_ON_ERROR);
            if (json_last_error() === JSON_ERROR_NONE) {
                $this->response = $decodedResponse;
            } else {
                $this->response = ['raw' => $response, 'json_error' => json_last_error_msg()];
            }
        } elseif (str_contains($contentType, 'text/html')) {
            $this->response = ['html' => $response];
        } else {
            $this->response = ['raw' => $response];
        }

        curl_close($this->ch);

        return $this;
    }

}