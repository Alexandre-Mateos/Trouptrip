<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractApiTestCase extends ApiTestCase
{
    static string $LOGIN_CHECK = '/api/login_check';
    protected string $defaultUrl;
    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    protected function get(string $class): ?object
    {
        return static::getContainer()->get($class);
    }

    protected function loginUser(string $email, string $password): void
    {
        $this->client->request('POST', self::$LOGIN_CHECK, [
            'headers' => [
                'Accept' => 'application/ld+json',
                'Content-Type' => 'application/ld+json',
            ],
            'json' => [
                'username' => $email,
                'password' => $password,
            ],
        ]);
    }

    protected function post(array $body): void
    {
        $this->client->request('POST', $this->defaultUrl, [
            'headers' => [
                'Accept' => 'application/ld+json',
                'Content-Type' => 'application/ld+json',
            ],
            'json' => $body,
        ]);
    }

    protected function patch(array $body, int $id): void
    {
        $url = $this->defaultUrl . '/' . $id;
        $this->client->request('PATCH', $url, [
            'headers' => [
                'Accept' => 'application/ld+json',
                'Content-Type' => 'application/merge-patch+json',
            ],
            'json' => $body,
        ]);
    }
}
