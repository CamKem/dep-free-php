<?php

namespace App\Services;

use App\Core\Http\External;
use JsonException;
use SensitiveParameter;

class MailChimpService
{
    protected string $apiKey;
    protected string $listId;
    protected string $dataCenter;
    protected string|array $response;
    protected int $status = 0;

    public function __construct(#[SensitiveParameter] string $listId)
    {
        $this->listId = $listId;
        $this->apiKey = env('MAILCHIMP_API_KEY');
        $this->dataCenter = substr($this->apiKey, strpos($this->apiKey, '-') + 1);
    }

    private function connect(string $url, string $method): External
    {
        return External::$method($url)
            ->wantsJson()
            ->withHeaders(['Authorization' => 'Basic ' . base64_encode('user:' . $this->apiKey)]);
    }

    private function handleResponse(External $response): void
    {
        $this->status = $response->status();
        $this->response = $response->response();
    }

    public function message(): ?string
    {
        return $this->response['detail'] ?? $this->response['error'] ?? null;
    }

    /**
     * @throws JsonException
     */
    public function subscribe(#[SensitiveParameter] string $email, array $mergeFields): self
    {
        $handler = $this->connect(
            url: 'https://' . $this->dataCenter . '.api.mailchimp.com/3.0/lists/' . $this->listId . '/members/',
            method: 'POST'
        )->withBody([
            'email_address' => strtolower($email),
            'status' => 'pending',
            'merge_fields' => $mergeFields
        ]);

        $this->handleResponse($handler->send());

        if ($this->status === 400) {
            if ($this->response['title'] !== "Forgotten Email Not Subscribed") {
                $this->response = ['error' => 'Failed to subscribe member'];
                return $this;
            }
            $this->update($email, $mergeFields);
        }

        return $this;
    }

    public function unsubscribe(#[SensitiveParameter] string $email): self
    {
        $memberId = md5(strtolower($email));
        $url = 'https://' . $this->dataCenter . '.api.mailchimp.com/3.0/lists/' . $this->listId . '/members/' . $memberId;
        $this->handleResponse($this->connect($url, 'DELETE')
            ->send());

        return $this;
    }

    /**
     * @throws JsonException
     */
    public function update(#[SensitiveParameter] string $email, array $mergeFields): self
    {
        $memberId = md5(strtolower($email));
        $url = 'https://' . $this->dataCenter . '.api.mailchimp.com/3.0/lists/' . $this->listId . '/members/' . $memberId;
        $handler = $this->connect($url, 'PUT')
            ->withBody([
                'status' => 'subscribed',
                'email_address' => $email,
                'merge_fields' => $mergeFields
            ]);

        $this->handleResponse($handler->send());

        if ($this->status === 400) {
            if ($this->response['title'] !== "Forgotten Email Not Subscribed") {
                $this->response = ['error' => 'Failed to update member'];
                return $this;
            }
            $this->unsubscribe($email);
            return $this->subscribe($email, $mergeFields);
        }

        return $this;
    }

    /**
     * @throws JsonException
     */
    public function getMember(#[SensitiveParameter] string $email): self
    {
        $url = 'https://' . $this->dataCenter . '.api.mailchimp.com/3.0/lists/' . $this->listId . '/members/' . $email;
        $response = $this->connect($url, 'GET')
            ->send();

        $this->status = $response->status();
        $this->response = $this->status === 200 ? $response->response('detail') : ['error' => 'Member not found'];

        return $this;
    }

    /**
     * @throws JsonException
     */
    public function getMembers(): self
    {
        $url = 'https://' . $this->dataCenter . '.api.mailchimp.com/3.0/lists/' . $this->listId . '/members/';
        $response = $this->connect($url, 'GET')->send();

        $this->status = $response->status();
        $this->response = $this->status === 200 ? $response->response() : ['error' => 'Failed to get members'];

        return $this;
    }
}