<?php declare(strict_types=1);
// ==== ./src/RESTAuthDriver.php ====

namespace PHPExperts\RESTSpeaker;

interface RESTAuthDriver
{
    public function setApiClient(RESTSpeaker $apiClient): void;
    public function generateGuzzleAuthOptions(): array;
}
