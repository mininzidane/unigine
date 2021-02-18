<?php
declare(strict_types=1);

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class ConvertControllerTest extends WebTestCase
{
    private \Symfony\Bundle\FrameworkBundle\KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testConvertSuccess(): void
    {
        $this->client->request('GET', '/api/v1/convert/USD/RUB/100');
        $response = $this->client->getResponse();
        self::assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $json = \json_decode($response->getContent(), true);
        self::assertTrue($json['success']);
        self::assertGreaterThan(5000, $json['value']);
    }

    public function testConvertIllegalCurrencies(): void
    {
        $this->client->request('GET', '/api/v1/convert/some/value/100');
        $response = $this->client->getResponse();
        self::assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $json = \json_decode($response->getContent(), true);
        self::assertFalse($json['success']);
        self::assertCount(2, $json['errors']);
    }

    public function testConvertNotExistCurrency(): void
    {
        $this->client->request('GET', '/api/v1/convert/TTT/RUB/100');
        $response = $this->client->getResponse();
        self::assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $json = \json_decode($response->getContent(), true);
        self::assertFalse($json['success']);
        self::assertEquals('No data for currency \'TTT\'', $json['error']);
    }
}
