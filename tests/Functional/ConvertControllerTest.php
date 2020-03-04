<?php
declare(strict_types=1);

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class ConvertControllerTest extends WebTestCase
{
    /**
     * @var \Symfony\Bundle\FrameworkBundle\KernelBrowser
     */
    private $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testConvertSuccess(): void
    {
        $this->client->request('GET', '/api/v1/convert/USD/RUB/100');
        $response = $this->client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $json = \json_decode($response->getContent(), true);
        $this->assertTrue($json['success']);
        $this->assertGreaterThan(5000, $json['value']);
    }

    public function testConvertIllegalCurrencies(): void
    {
        $this->client->request('GET', '/api/v1/convert/some/value/100');
        $response = $this->client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $json = \json_decode($response->getContent(), true);
        $this->assertFalse($json['success']);
        $this->assertCount(2, $json['errors']);
    }

    public function testConvertNotExistCurrency(): void
    {
        $this->client->request('GET', '/api/v1/convert/TTT/RUB/100');
        $response = $this->client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $json = \json_decode($response->getContent(), true);
        $this->assertFalse($json['success']);
        $this->assertEquals('No data for currency \'TTT\'', $json['error']);
    }
}
