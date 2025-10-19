<?php declare(strict_types=1);
// ==== src/NoAuth.php ====

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

namespace PHPExperts\RESTSpeaker;

class NoAuth implements RESTAuthDriver
{
    public const AUTH_NONE = 'NoAuth';

    protected ?RESTSpeaker $api = null;

    protected string $authMode;

    public function __construct(?RESTSpeaker $apiClient = null)
    {
        $this->api = $apiClient;
        $this->authMode = self::AUTH_NONE;
    }

    protected function generateOAuth2TokenOptions(): array
    {
        return [];
    }

    protected function generatePasskeyOptions(): array
    {
        return [];
    }

    public function setApiClient(RESTSpeaker $apiClient): void
    {
        $this->api = $apiClient;
    }

    public function generateGuzzleAuthOptions(): array
    {
        return [];
    }
}
