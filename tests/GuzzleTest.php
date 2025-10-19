<?php declare(strict_types=1);
// ==== ./tests/GuzzleTest.php ====

/**
 * This file is part of RESTSpeaker, a PHP Experts, Inc., Project.
 *
 * Copyright © 2019-2025 PHP Experts, Inc.
 * Author: Theodore R. Smith <theodore@phpexperts.pro>
 *  GPG Fingerprint: 4BF8 2613 1C34 87AC D28F  2AD8 EB24 A91D D612 5690
 *  https://www.phpexperts.pro/
 *  https://github.com/phpexpertsinc/RESTSpeaker
 *
 * This file is licensed under the MIT License.
 */

namespace PHPExperts\RESTSpeaker\Tests;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPExperts\RESTSpeaker\HTTPSpeaker;
use PHPExperts\RESTSpeaker\RESTSpeaker;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

/** @testdox Tests for Guzzle ClientInterface methods */
class GuzzleTest extends TestCase
{
    /** @var RESTSpeaker */
    protected $api;

    /** @var MockHandler */
    protected $guzzleHandler;

    public function setUp(): void
    {
        parent::setUp();

        $restAuthMock = TestHelper::buildRESTAuthMock();

        $this->guzzleHandler = new MockHandler();

        $http = new HTTPSpeaker('', new GuzzleClient(['handler' => $this->guzzleHandler]));

        $this->api = new RESTSpeaker($restAuthMock, '', $http);
    }

    /** @testdox send() delegates to HTTPSpeaker and returns ResponseInterface */
    public function testSendDelegatesToHTTPSpeaker()
    {
        $expectedResponse = new Response(200, ['Content-Type' => 'application/json'], '{"success":true}');
        $this->guzzleHandler->append($expectedResponse);

        $request = new Request('GET', 'https://api.example.com/users');
        $response = $this->api->send($request);

        self::assertInstanceOf(ResponseInterface::class, $response);
        self::assertEquals(200, $response->getStatusCode());
        self::assertEquals('{"success":true}', (string) $response->getBody());
    }

    /** @testdox send() passes options through correctly */
    public function testSendPassesOptionsThrough()
    {
        $this->guzzleHandler->append(new Response(200));

        $request = new Request('GET', 'https://api.example.com/users');
        $options = ['timeout' => 10, 'headers' => ['X-Custom' => 'value']];
        
        $this->api->send($request, $options);

        $lastRequest = $this->guzzleHandler->getLastRequest();
        self::assertInstanceOf(Request::class, $lastRequest);
        self::assertEquals('GET', $lastRequest->getMethod());
    }

    /** @testdox sendAsync() returns a PromiseInterface */
    public function testSendAsyncReturnsPromise()
    {
        $this->guzzleHandler->append(new Response(200, [], '{"async":true}'));

        $request = new Request('POST', 'https://api.example.com/jobs');
        $promise = $this->api->sendAsync($request);

        self::assertInstanceOf(PromiseInterface::class, $promise);
        
        // Wait for the promise to resolve
        $response = $promise->wait();
        self::assertInstanceOf(ResponseInterface::class, $response);
        self::assertEquals('{"async":true}', (string) $response->getBody());
    }

    /** @testdox sendAsync() passes options through correctly */
    public function testSendAsyncPassesOptionsThrough()
    {
        $this->guzzleHandler->append(new Response(201));

        $request = new Request('POST', 'https://api.example.com/webhooks');
        $options = ['timeout' => 5];
        
        $promise = $this->api->sendAsync($request, $options);
        $response = $promise->wait();

        self::assertEquals(201, $response->getStatusCode());
    }

    /** @testdox request() delegates to HTTPSpeaker and returns ResponseInterface */
    public function testRequestDelegatesToHTTPSpeaker()
    {
        $expectedResponse = new Response(200, [], 'response body');
        $this->guzzleHandler->append($expectedResponse);

        $response = $this->api->request('GET', '/endpoint');

        self::assertInstanceOf(ResponseInterface::class, $response);
        self::assertEquals('response body', (string) $response->getBody());
    }

    /** @testdox request() works with all HTTP methods */
    public function testRequestWorksWithAllHTTPMethods()
    {
        $methods = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'];

        foreach ($methods as $method) {
            $this->guzzleHandler->append(new Response(200));
            
            $response = $this->api->request($method, '/test');
            
            self::assertInstanceOf(ResponseInterface::class, $response);
            self::assertEquals($method, $this->guzzleHandler->getLastRequest()->getMethod());
        }
    }

    /** @testdox request() passes options through correctly */
    public function testRequestPassesOptionsThrough()
    {
        $this->guzzleHandler->append(new Response(200));

        $options = [
            'headers' => ['Authorization' => 'Bearer token123'],
            'query' => ['page' => 1],
        ];
        
        $this->api->request('GET', '/users', $options);

        $lastRequest = $this->guzzleHandler->getLastRequest();
        self::assertTrue($lastRequest->hasHeader('Authorization'));
    }

    /** @testdox requestAsync() returns a PromiseInterface */
    public function testRequestAsyncReturnsPromise()
    {
        $this->guzzleHandler->append(new Response(202, [], '{"queued":true}'));

        $promise = $this->api->requestAsync('POST', '/queue');

        self::assertInstanceOf(PromiseInterface::class, $promise);
        
        $response = $promise->wait();
        self::assertEquals(202, $response->getStatusCode());
    }

    /** @testdox requestAsync() works with all HTTP methods */
    public function testRequestAsyncWorksWithAllHTTPMethods()
    {
        $methods = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'];

        foreach ($methods as $method) {
            $this->guzzleHandler->append(new Response(200));
            
            $promise = $this->api->requestAsync($method, '/async-test');
            $response = $promise->wait();
            
            self::assertInstanceOf(ResponseInterface::class, $response);
            self::assertEquals($method, $this->guzzleHandler->getLastRequest()->getMethod());
        }
    }

    /** @testdox requestAsync() passes options through correctly */
    public function testRequestAsyncPassesOptionsThrough()
    {
        $this->guzzleHandler->append(new Response(200));

        $options = ['json' => ['data' => 'value']];
        
        $promise = $this->api->requestAsync('POST', '/data', $options);
        $response = $promise->wait();

        self::assertEquals(200, $response->getStatusCode());
        
        $lastRequest = $this->guzzleHandler->getLastRequest();
        self::assertStringContainsString('application/json', $lastRequest->getHeaderLine('Content-Type'));
    }

    /** @testdox Low-level methods work with full URIs */
    public function testLowLevelMethodsWorkWithFullURIs()
    {
        $this->guzzleHandler->append(new Response(200));
        
        $response = $this->api->request('GET', 'https://external-api.com/data');
        
        self::assertInstanceOf(ResponseInterface::class, $response);
        self::assertEquals('external-api.com', $this->guzzleHandler->getLastRequest()->getUri()->getHost());
    }

    /** @testdox send() handles PSR-7 Request objects correctly */
    public function testSendHandlesPSR7RequestCorrectly()
    {
        $this->guzzleHandler->append(new Response(201));

        $request = new Request(
            'POST',
            'https://api.example.com/resources',
            ['Content-Type' => 'application/json'],
            '{"name":"test"}'
        );

        $response = $this->api->send($request);

        self::assertEquals(201, $response->getStatusCode());
        
        $lastRequest = $this->guzzleHandler->getLastRequest();
        self::assertEquals('POST', $lastRequest->getMethod());
        self::assertEquals('{"name":"test"}', (string) $lastRequest->getBody());
    }
}
