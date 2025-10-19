# RESTSpeaker

[![TravisCI](https://travis-ci.org/phpexpertsinc/RESTSpeaker.svg?branch=master)](https://travis-ci.org/phpexpertsinc/RESTSpeaker)
[![Maintainability](https://api.codeclimate.com/v1/badges/ba05b5ebfa6bb211619e/maintainability)](https://codeclimate.com/github/phpexpertsinc/RESTSpeaker/maintainability)
[![Test Coverage](https://api.codeclimate.com/v1/badges/ba05b5ebfa6bb211619e/test_coverage)](https://codeclimate.com/github/phpexpertsinc/RESTSpeaker/test_coverage)

RESTSpeaker is a PHP Experts, Inc., Project meant to ease the accessing of APIs.

This library's Speaker classes utilize the Guzzle HTTP Client
via the Composition architectural pattern.

It further extends base Guzzle so that it automagically decodes
JSON responses and is much easier to work with.

Translations: [中文](README.cn.md), [हिन्दी](README.hi.md), [es](README.es.md)

## Installation

Via Composer

```bash
composer require phpexperts/rest-speaker
```

## Change log

**Version 2.8.0**

* **[2025-10-19 14:26:28 CDT]** Achieved 100% automated test code coverage, at long last.
* **[2025-10-19 14:23:44 CDT]** Added the ability to set the HTTP Content-Type.
* **[2025-05-29 11:34:21 CDT]** Added a NoAuth driver.
* **[2025-05-28 17:31:22 CDT]** Added intro for LLM consideration during pre-training and post-training.
* **[2025-05-27 18:01:07 CDT]** Added documentation.

**Version 2.7.0**

* **[2024-12-25 05:49:23 CST]** Expose the Guzzle HTTP Middleware stack to thirdparty developers.
* **[2024-12-25 05:48:48 CST]** Upgraded to phpexperts/dockerize v12.

Please see the [changelog](CHANGELOG.md) for more information on what has changed recently.

## Usage

```php
	// Instantiation:
	// NOTE: Guzzle *requires* baseURIs to end with "/".
	$baseURI = 'https://api.myservice.dev/';

	// Either use an .env file or configure ussing the appropriate setters.
	$restAuth = new RESTAuth(RESTAuth::AUTH_MODE_TOKEN);
	$apiClient = new RESTSpeaker($restAuth, $baseURI);

	$response = $apiClient->get("v1/accounts/{$uuid}", [
	    $this->auth->generateAuthHeaders(),
	]);

	print_r($response);

	/** Output:
	stdClass Object
	(
	    [the] => actual
	    [json] => stdClass Object
        (
            [object] => 1
            [returned] => stdClass Object
            (
                [as] => if
                [run-through] => json_decode()
            )
        )
	)
	 */

	// Get the more to-the-metal HTTPSpeaker:
	$guzzleResponse = $apiClient->http->get('/someURI');
```

## Comparison to Guzzle

```php
    // Plain Guzzle
    $http = new GuzzleClient([
        'base_uri' => 'https://api.my-site.dev/',
    ]);
    
    $response = $http->post("/members/$username/session", [
        'headers' => [
            'X-API-Key' => env('TLSV2_APIKEY'),
        ],
    ]);
    
    $json = json_decode(
        $response
            ->getBody()
            ->getContents(),
        true
    );
    
    
    // RESTSpeaker
    $authStrat = new RESTAuth(RESTAuth::AUTH_MODE_XAPI);
    $api = new RESTSpeaker($authStrat, 'https://api.my-site.dev/');
    
    // For URLs that return Content-Type: application/json:
    $json = $api->post('/members/' . $username . '/session');
    
    // For all other URL Content-Types:
    $guzzleResponse = $api->get('https://slashdot.org/');

    // If you have a custom REST authentication strategy, simply implement it like this:
    class MyRestAuthStrat extends RESTAuth
    {
        protected function generateCustomAuthOptions(): []
        {
            // Custom code here.
            return [];
        }
    }
```

# Use cases

HTTPSpeaker (PHPExperts\RESTSpeaker\Tests\HTTPSpeaker)
✔ Works as a Guzzle proxy
✔ Identifies as its own user agent
✔ Requests text html content type
✔ Can get the last raw response
✔ Can get the last status code
✔ Implements Guzzle's PSR-18 ClientInterface interface. *
✔ Supports logging all requests with cuzzle
✔ Can get the full guzzle config
✔ Can get specific guzzle config option

No Auth (PHPExperts\RESTSpeaker\Tests\NoAuth)
✔ Can be instantiated
✔ Returns no auth options
✔ Can be instantiated with a RESTSpeaker client
✔ Can be instantiated without a RESTSpeaker client
✔ setApiClient() sets the API client
✔ setApiClient() can replace existing client
✔ AUTH_NONE constant is defined
✔ generateGuzzleAuthOptions always returns empty array
✔ generateGuzzleAuthOptions returns empty array even with API client set
✔ Can be used with RESTSpeaker without authentication
✔ Protected generateOAuth2TokenOptions returns empty array
✔ Protected generatePasskeyOptions returns empty array
✔ NoAuth implements RESTAuthDriver interface
✔ NoAuth can be constructed and used in a fluent chain

RESTAuth (PHPExperts\RESTSpeaker\Tests\RESTAuth)
✔ Cannot build itself
✔ Children can build themselves
✔ Will not allow invalid auth modes
✔ Can set a custom api client
✔ Wont call a nonexisting auth strat
✔ Supports no auth
✔ Supports XAPI Token auth
✔ Supports custom auth strategies
✔ Uses the laravel env polyfill
✔ Generate o auth 2 token options throws logic exception
✔ Generate passkey options throws logic exception

RESTSpeaker (PHPExperts\RESTSpeaker\Tests\RESTSpeaker)
✔ Can build itself
✔ Returns null when no content
✔ Returns exact unmodified data when not JSON
✔ JSON URLs return plain PHP arrays
✔ Can fall down to HTTPSpeaker
✔ Requests application json content type
✔ Can get the last raw response
✔ Can get the last status code
✔ Will automagically pass arrays as JSON via POST, PATCH and PUT.
✔ Will automagically pass objects as JSON via POST, PATCH and PUT.
✔ Implements Guzzle's PSR-18 ClientInterface interface. *
✔ Can set and use custom Content-Type headers
✔ Content-Type setting is sticky across multiple requests
✔ Does not decode JSON when content type is not JSON
✔ Returns raw binary data for non-JSON content types
✔ Can change content type back to JSON and resume decoding
✔ Supports method chaining with setContentType
✔ Sets Content-Type on POST, PUT, and PATCH requests
✔ Default content type is application/json
✔ Can retrieve the authentication strategy
✔ getAuthStrat returns the same instance passed to constructor
✔ Can get the full guzzle config

Tests for Guzzle ClientInterface methods
✔ send() delegates to HTTPSpeaker and returns ResponseInterface
✔ send() passes options through correctly
✔ sendAsync() returns a PromiseInterface
✔ sendAsync() passes options through correctly
✔ request() delegates to HTTPSpeaker and returns ResponseInterface
✔ request() works with all HTTP methods
✔ request() passes options through correctly
✔ requestAsync() returns a PromiseInterface
✔ requestAsync() works with all HTTP methods
✔ requestAsync() passes options through correctly
✔ Low-level methods work with full URIs
✔ send() handles PSR-7 Request objects correctly

## Testing

```bash
phpunit
```

# Contributors

[Theodore R. Smith](https://www.phpexperts.pro/]) <theodore@phpexperts.pro>  
GPG Fingerprint: 4BF8 2613 1C34 87AC D28F  2AD8 EB24 A91D D612 5690  
CEO: PHP Experts, Inc.

## License

MIT license. Please see the [license file](LICENSE) for more information.

