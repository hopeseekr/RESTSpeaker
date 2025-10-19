<?php declare(strict_types=1);

/**
 * This file is part of RESTSpeaker, a PHP Experts, Inc., Project.
 *
 * Copyright © 2019-2024 PHP Experts, Inc.
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
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPExperts\RESTSpeaker\HTTPSpeaker;
use PHPExperts\RESTSpeaker\RESTSpeaker;
use PHPUnit\Framework\TestCase;

class RESTSpeakerTest extends TestCase
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

    public function testCanBuildItself()
    {
        $api = new RESTSpeaker(TestHelper::buildRESTAuthMock());
        self::assertInstanceOf(RESTSpeaker::class, $api);
    }

    public function testReturnsNullWhenNoContent()
    {
        $this->guzzleHandler->append(
            new Response(
                204, // HTTP/204: No Content
                ['Content-Type' => 'application/json'],
                null
            )
        );

        $actual = $this->api->get('/no-data');
        self::assertNull($actual);
    }

    public function testReturns_exact_unmodified_data_when_not_JSON()
    {
        $expectedBody = '<html lang="us">Hi</html>';
        $expected = new Response(200, ['Content-Type' => 'text/html'], $expectedBody);
        $this->guzzleHandler->append(
            $expected
        );

        $actual = $this->api->get('https://somewhere.com/');
        self::assertEquals($expectedBody, $actual);
    }

    public function testJSON_URLs_return_plain_PHP_arrays()
    {
        $expected = [
            'decoded' => 'json',
            'hmm' => [
                'nested',
                'array',
                1,
                2.0,
            ],
        ];

        $json = json_encode($expected);
        $this->guzzleHandler->append(
            new Response(
                200,
                ['Content-Type' => 'application/json'],
                $json
            )
        );

        $expected = json_decode($json);
        $actual = $this->api->get('https://somewhere.com/');
        self::assertEquals($expected, $actual);
    }

    public function testCan_fall_down_to_HTTPSpeaker()
    {
        $expectedBody = json_encode([
            'decoded' => 'json',
            'hmm' => [
                'nested',
                'array',
                1,
                2.0,
            ],
        ]);

        $response = new Response(
            200,
            ['Content-Type' => 'application/json'],
            $expectedBody
        );

        $this->guzzleHandler->append(
            $response
        );

        $actual = $this->api->http->get('https://somewhere.com/');
        self::assertEquals($response, $actual);
        self::assertEquals($expectedBody, $actual->getBody());
    }

    public function testRequestsApplicationJsonContentType()
    {
        $this->guzzleHandler->append(
            new Response(200, [], '')
        );

        $this->api->get('https://somewhere.com/');
        $requestHeaders = $this->guzzleHandler->getLastRequest()->getHeaders();

        $expected = 'application/json';
        self::assertEquals($expected, $requestHeaders['Content-Type'][0]);
    }

    public function testCanGetTheLastRawResponse()
    {
        // Test returns null with no request.
        self::assertSame(null, $this->api->getLastResponse());

        // Test normal requests.
        $statuses = [
            new Response(200, ['Content-Type' => 'application/json'], '{"hello": "world"}'),
            new Response(204, [], ''),
            new Response(400, [], ''),
        ];

        foreach ($statuses as $expected) {
            $expected = $statuses[0];
            $this->guzzleHandler->append($expected);
            $expectedJson = json_decode((string) $expected->getBody());

            $actualJSON = $this->api->get('https://somewhere.com/');
            $this->assertSame($expected, $this->api->getLastResponse());
            $this->assertEquals($expectedJson, $actualJSON);
        }
    }

    public function testCanGetTheLastStatusCode()
    {
        // Test returns -1 with no request.
        self::assertSame(-1, $this->api->getLastStatusCode());

        // Test normal requests.
        $statuses = [
            200 => new Response(200, ['Content-Type' => 'application/json'], '{"hello": "world"}'),
            204 => new Response(204, [], ''),
            400 => new Response(400, [], ''),
        ];

        foreach ($statuses as $expected => $statusResponse) {
            $this->guzzleHandler->append($statusResponse);

            $this->api->get('https://somewhere.com/');
            $this->assertSame($expected, $this->api->getLastStatusCode());
        }
    }

    /** @testdox Will automagically pass arrays as JSON via POST, PATCH and PUT. */
    public function testWillAutomagicallyPassJSONArrays()
    {
        $methods = ['post', 'patch', 'put'];
        foreach ($methods as $method) {
            $expectedJSON = sprintf('{"hello":"%s"}', $method);
            $expectedResponse = json_decode($expectedJSON);
            $payload = json_decode($expectedJSON, true);
            $this->guzzleHandler->append(new Response(200, ['Content-Type' => 'application/json'], $expectedJSON));

            $response = $this->api->$method("/$method", $payload);
            self::assertEquals($expectedResponse, $response);

            $actualRequest = $this->guzzleHandler->getLastRequest();
            self::assertInstanceOf(Request::class, $actualRequest);

            $actualJSON = (string) $actualRequest->getBody();
            self::assertEquals($expectedJSON, $actualJSON);
        }
    }

    /** @testdox Will automagically pass objects as JSON via POST, PATCH and PUT. */
    public function testWillAutomagicallyPassJSONObjects()
    {
        $methods = ['post', 'patch', 'put'];
        foreach ($methods as $method) {
            $expectedJSON = sprintf('{"hello":"%s"}', $method);
            $expectedResponse = json_decode($expectedJSON);
            $payload = json_decode($expectedJSON);
            $this->guzzleHandler->append(new Response(200, ['Content-Type' => 'application/json'], $expectedJSON));

            $response = $this->api->$method("/$method", $payload);
            self::assertEquals($expectedResponse, $response);

            $actualRequest = $this->guzzleHandler->getLastRequest();
            self::assertInstanceOf(Request::class, $actualRequest);

            $actualJSON = (string) $actualRequest->getBody();
            self::assertEquals($expectedJSON, $actualJSON);
        }
    }

    /** @testdox Implements Guzzle's PSR-18 ClientInterface interface. **/
    public function testImplementsGuzzlesClientInterface()
    {
        self::assertInstanceOf(\GuzzleHttp\ClientInterface::class, $this->api);
    }

    // ============================================
    // Content-Type Configuration Tests
    // ============================================

    /** @testdox Can set and use custom Content-Type headers */
    public function testCanSetContentType()
    {
        $this->guzzleHandler->append(
            new Response(200, [], 'some data')
        );

        $result = $this->api->setContentType('application/pdf');

        // Should return self for method chaining
        self::assertSame($this->api, $result);

        $this->api->get('https://somewhere.com/');
        $requestHeaders = $this->guzzleHandler->getLastRequest()->getHeaders();

        self::assertEquals('application/pdf', $requestHeaders['Content-Type'][0]);
        self::assertEquals('application/pdf', $requestHeaders['Accept'][0]);
    }

    /** @testdox Content-Type setting is sticky across multiple requests */
    public function testContentTypeIsStickyAcrossRequests()
    {
        $this->api->setContentType('application/xml');

        // First request
        $this->guzzleHandler->append(new Response(200, [], '<xml/>'));
        $this->api->get('https://somewhere.com/first');
        $firstHeaders = $this->guzzleHandler->getLastRequest()->getHeaders();

        // Second request - should still use the same content type
        $this->guzzleHandler->append(new Response(200, [], '<xml/>'));
        $this->api->post('https://somewhere.com/second', null);
        $secondHeaders = $this->guzzleHandler->getLastRequest()->getHeaders();

        self::assertEquals('application/xml', $firstHeaders['Content-Type'][0]);
        self::assertEquals('application/xml', $firstHeaders['Accept'][0]);
        self::assertEquals('application/xml', $secondHeaders['Content-Type'][0]);
        self::assertEquals('application/xml', $secondHeaders['Accept'][0]);
    }

    /** @testdox Does not decode JSON when content type is not JSON */
    public function testDoesNotDecodeJSONWhenContentTypeIsNotJSON()
    {
        $jsonString = '{"hello":"world"}';

        $this->guzzleHandler->append(
            new Response(200, ['Content-Type' => 'text/plain'], $jsonString)
        );

        $this->api->setContentType('text/plain');
        $actual = $this->api->get('https://somewhere.com/');

        // Should return raw string, not decoded object
        self::assertIsString($actual);
        self::assertEquals($jsonString, $actual);
    }

    /** @testdox Returns raw binary data for non-JSON content types */
    public function testReturnsRawBinaryDataForNonJSONContentTypes()
    {
        $pdfData = '%PDF-1.4 binary content here...';

        $this->guzzleHandler->append(
            new Response(200, ['Content-Type' => 'application/pdf'], $pdfData)
        );

        $this->api->setContentType('application/pdf');
        $actual = $this->api->get('https://somewhere.com/document.pdf');

        self::assertEquals($pdfData, $actual);
    }

    /** @testdox Can change content type back to JSON and resume decoding */
    public function testCanChangeContentTypeBackToJSON()
    {
        // First, use a non-JSON content type
        $this->api->setContentType('text/plain');
        $this->guzzleHandler->append(new Response(200, [], 'plain text'));
        $plainResult = $this->api->get('https://somewhere.com/plain');
        self::assertIsString($plainResult);

        // Now switch back to JSON
        $this->api->setContentType('application/json');
        $jsonString = '{"decoded":"json"}';
        $this->guzzleHandler->append(
            new Response(200, ['Content-Type' => 'application/json'], $jsonString)
        );
        $jsonResult = $this->api->get('https://somewhere.com/json');

        // Should be decoded as an object
        self::assertIsObject($jsonResult);
        self::assertEquals('json', $jsonResult->decoded);
    }

    /** @testdox Supports method chaining with setContentType */
    public function testSupportsMethodChainingWithSetContentType()
    {
        $this->guzzleHandler->append(
            new Response(200, [], 'data')
        );

        // Chain setContentType with a request
        $result = $this->api
            ->setContentType('application/xml')
            ->get('https://somewhere.com/');

        $requestHeaders = $this->guzzleHandler->getLastRequest()->getHeaders();
        self::assertEquals('application/xml', $requestHeaders['Content-Type'][0]);
        self::assertEquals('data', $result);
    }

    /** @testdox Sets Content-Type on POST, PUT, and PATCH requests */
    public function testSetsContentTypeOnAllHTTPMethods()
    {
        $methods = ['post', 'put', 'patch'];

        $this->api->setContentType('application/xml');

        foreach ($methods as $method) {
            $this->guzzleHandler->append(new Response(200, [], '<response/>'));

            $this->api->$method('https://somewhere.com/', ['data' => 'test']);
            $requestHeaders = $this->guzzleHandler->getLastRequest()->getHeaders();

            self::assertEquals('application/xml', $requestHeaders['Content-Type'][0],
                "Content-Type should be set for $method");
            self::assertEquals('application/xml', $requestHeaders['Accept'][0],
                "Accept header should be set for $method");
        }
    }

    /** @testdox Default content type is application/json */
    public function testDefaultContentTypeIsApplicationJson()
    {
        $this->guzzleHandler->append(
            new Response(200, ['Content-Type' => 'application/json'], '{"test":"value"}')
        );

        // Don't set content type - use default
        $result = $this->api->get('https://somewhere.com/');

        // Should decode JSON by default
        self::assertIsObject($result);
        self::assertEquals('value', $result->test);

        $requestHeaders = $this->guzzleHandler->getLastRequest()->getHeaders();
        self::assertEquals('application/json', $requestHeaders['Content-Type'][0]);
        self::assertEquals('application/json', $requestHeaders['Accept'][0]);
    }

    /** @testdox Can retrieve the authentication strategy */
    public function testCanGetTheAuthStrat()
    {
        $authStrat = $this->api->getAuthStrat();

        self::assertInstanceOf(\PHPExperts\RESTSpeaker\RESTAuthDriver::class, $authStrat);
    }

    /** @testdox getAuthStrat returns the same instance passed to constructor */
    public function testGetAuthStratReturnsSameInstance()
    {
        $customAuth = TestHelper::buildRESTAuthMock();
        $api = new RESTSpeaker($customAuth);

        $retrievedAuth = $api->getAuthStrat();

        self::assertSame($customAuth, $retrievedAuth);
    }

    public function testCanGetTheFullGuzzleConfig()
    {
        $config = $this->api->getConfig();

        self::assertIsArray($config);

        self::assertArrayHasKey('decode_content', $config);
        self::assertTrue($config['http_errors']);
        self::assertArrayHasKey('handler', $config);
    }
}
