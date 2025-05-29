<?php declare(strict_types=1);
// ==== ./tests/NoAuthTest.php ====

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

use Error;
use LogicException;
use PHPExperts\RESTSpeaker\NoAuth;
use PHPExperts\RESTSpeaker\RESTAuth;
use PHPExperts\RESTSpeaker\RESTAuthDriver;
use PHPExperts\RESTSpeaker\RESTSpeaker;
use PHPUnit\Framework\TestCase;

class NoAuthTest extends TestCase
{
    public function testCanBeInstantiated()
    {
        $noAuth = new NoAuth();
        self::assertInstanceOf(NoAuth::class, $noAuth);
        self::assertInstanceOf(RESTAuthDriver::class, $noAuth);
        self::assertNotInstanceOf(RESTAuth::class, $noAuth);
    }

    public function testReturnsNoAuthOptions()
    {
        $noAuth = new NoAuth();
        self::assertEquals([], $noAuth->generateGuzzleAuthOptions());
    }

    /** @testdox Can be instantiated with a RESTSpeaker client */
    public function testCanBeInstantiatedWithRESTSpeaker()
    {
        $apiClient = $this->createMock(RESTSpeaker::class);
        $noAuth = new NoAuth($apiClient);

        self::assertInstanceOf(NoAuth::class, $noAuth);
    }

    /** @testdox Can be instantiated without a RESTSpeaker client */
    public function testCanBeInstantiatedWithoutRESTSpeaker()
    {
        $noAuth = new NoAuth(null);

        self::assertInstanceOf(NoAuth::class, $noAuth);
    }

    /** @testdox setApiClient() sets the API client */
    public function testSetApiClientSetsTheClient()
    {
        $noAuth = new NoAuth();
        $apiClient = $this->createMock(RESTSpeaker::class);

        $noAuth->setApiClient($apiClient);

        // Since $api is protected, we verify by checking no exceptions were thrown
        self::assertInstanceOf(NoAuth::class, $noAuth);
    }

    /** @testdox setApiClient() can replace existing client */
    public function testSetApiClientCanReplaceExistingClient()
    {
        $firstClient = $this->createMock(RESTSpeaker::class);
        $secondClient = $this->createMock(RESTSpeaker::class);

        $noAuth = new NoAuth($firstClient);
        $noAuth->setApiClient($secondClient);

        // Verify method completes without error
        self::assertInstanceOf(NoAuth::class, $noAuth);
    }

    /** @testdox AUTH_NONE constant is defined */
    public function testAuthNoneConstantIsDefined()
    {
        self::assertEquals('NoAuth', NoAuth::AUTH_NONE);
    }

    /** @testdox generateGuzzleAuthOptions always returns empty array */
    public function testGenerateGuzzleAuthOptionsAlwaysReturnsEmptyArray()
    {
        $noAuth = new NoAuth();

        // Call multiple times to ensure consistency
        self::assertEquals([], $noAuth->generateGuzzleAuthOptions());
        self::assertEquals([], $noAuth->generateGuzzleAuthOptions());
        self::assertEquals([], $noAuth->generateGuzzleAuthOptions());
    }

    /** @testdox generateGuzzleAuthOptions returns empty array even with API client set */
    public function testGenerateGuzzleAuthOptionsReturnsEmptyArrayWithApiClient()
    {
        $apiClient = $this->createMock(RESTSpeaker::class);
        $noAuth = new NoAuth($apiClient);

        self::assertEquals([], $noAuth->generateGuzzleAuthOptions());
    }

    /** @testdox Can be used with RESTSpeaker without authentication */
    public function testCanBeUsedWithRESTSpeaker()
    {
        $noAuth = new NoAuth();
        $api = new RESTSpeaker($noAuth, 'https://api.example.com');

        self::assertInstanceOf(RESTSpeaker::class, $api);
        self::assertSame($noAuth, $api->getAuthStrat());
    }

    /** @testdox Protected generateOAuth2TokenOptions returns empty array */
    public function testProtectedGenerateOAuth2TokenOptionsReturnsEmptyArray()
    {
        $noAuth = new class extends NoAuth {
            public function exposeGenerateOAuth2TokenOptions(): array
            {
                return $this->generateOAuth2TokenOptions();
            }
        };

        self::assertEquals([], $noAuth->exposeGenerateOAuth2TokenOptions());
    }

    /** @testdox Protected generatePasskeyOptions returns empty array */
    public function testProtectedGeneratePasskeyOptionsReturnsEmptyArray()
    {
        $noAuth = new class extends NoAuth {
            public function exposeGeneratePasskeyOptions(): array
            {
                return $this->generatePasskeyOptions();
            }
        };

        self::assertEquals([], $noAuth->exposeGeneratePasskeyOptions());
    }

    /** @testdox NoAuth implements RESTAuthDriver interface */
    public function testImplementsRESTAuthDriverInterface()
    {
        $noAuth = new NoAuth();

        self::assertInstanceOf(RESTAuthDriver::class, $noAuth);
        self::assertTrue(method_exists($noAuth, 'generateGuzzleAuthOptions'));
        self::assertTrue(method_exists($noAuth, 'setApiClient'));
    }

    /** @testdox NoAuth can be constructed and used in a fluent chain */
    public function testCanBeUsedInFluentChain()
    {
        $noAuth = new NoAuth();
        $apiClient = $this->createMock(RESTSpeaker::class);

        $noAuth->setApiClient($apiClient);
        $options = $noAuth->generateGuzzleAuthOptions();

        self::assertEquals([], $options);
    }
}
