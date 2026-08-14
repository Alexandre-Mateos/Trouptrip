<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\ResponseInterface;

abstract class AbstractApiTestCase extends ApiTestCase
{
    static string $LOGIN_CHECK = '/api/login_check';
    protected string $defaultUrl;
    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    protected function getEntity(string $class): ?object
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

    protected function delete(int $id): void
    {
        $url = $this->defaultUrl . '/' . $id;

        $this->client->request('DELETE', $url, [
            'headers' => [
                'Accept' => 'application/ld+json',
            ],
        ]);
    }

    protected function get(): ResponseInterface
    {
        return $this->client->request(
            'GET',
            $this->defaultUrl,
            [
                'headers' => [
                    'Accept' => 'application/ld+json',
                ],
            ]
        );
    }

    protected function getDataCollection(): array
    {
        $response = $this->get();

        return $response->toArray()['member'];
    }
}
