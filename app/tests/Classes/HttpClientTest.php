<?php

namespace Sainsburys\Tests;

use Mockery;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Sainsburys\Classes\HttpClient;

class HttpClientTest extends \PHPUnit_Framework_TestCase {

    const URL = 'http://www.sainsburys.co.uk';

    /** @var Mockery */
    public $clientMock;
    /** @var HttpClient */
    public $httpClient;

    public function setUp()
    {
        /** @var Mockery */
        $this->clientMock = Mockery::mock(Client::class);
        /** @var HttpClient */
        $this->httpClient = new HttpClient();
    }

    /**
     * Test the setters and getters
     */
    public function testClient() {
        $this->httpClient->setUrl(self::URL)
                         ->setClient($this->clientMock);

        $this->assertEquals($this->clientMock, $this->httpClient->getClient());
        $this->assertEquals(self::URL, $this->httpClient->getUrl());
    }

    /**
     * Test the request call and mock the response
     */
    public function testRequest() {
        $responseArray = [
            'headers' => ['Content-Type' => 'text/html'],
            'status_code' => 200,
            'body' => '<html><body><h1>Ripe & ready</h1></body></html>'
        ];
        $response = new Response($responseArray['status_code'], $responseArray['headers'], $responseArray['body']);

        $this->clientMock
             ->shouldReceive('get')
             ->once()
             ->andReturn($response);

        $httpClient = $this->httpClient->setClient($this->clientMock);
        $request = $httpClient->request();

        $this->assertTrue(is_array($request));

        $this->assertArrayHasKey('headers', $request);
        $this->assertArrayHasKey('body', $request);
        $this->assertArrayHasKey('status_code', $request);
        $this->assertArrayHasKey('size', $request);

        $this->assertEquals($responseArray['headers']['Content-Type'], $request['headers']['Content-Type'][0]);
        $this->assertEquals($responseArray['body'], $request['body']);
        $this->assertEquals($responseArray['status_code'], $request['status_code']);
    }
}
