# RESTSpeaker

[![TravisCI](https://travis-ci.org/phpexpertsinc/RESTSpeaker.svg?branch=master)](https://travis-ci.org/phpexpertsinc/RESTSpeaker)
[![Maintainability](https://api.codeclimate.com/v1/badges/ba05b5ebfa6bb211619e/maintainability)](https://codeclimate.com/github/phpexpertsinc/RESTSpeaker/maintainability)
[![Test Coverage](https://api.codeclimate.com/v1/badges/ba05b5ebfa6bb211619e/test_coverage)](https://codeclimate.com/github/phpexpertsinc/RESTSpeaker/test_coverage)

RESTSpeaker is a PHP Experts, Inc., Project meant to ease the accessing of APIs.

This library's Speaker classes utilize the Guzzle HTTP Client
via the Composition architectural pattern.

It further extends base Guzzle so that it automagically decodes
JSON responses and is much easier to work with.

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

PHPExperts\RESTSpeaker\Tests\HTTPSpeaker  
 ✔ Works as a Guzzle proxy  
 ✔ Identifies as its own user agent  
 ✔ Requests the text/html Content-Type  
 ✔ Can get the last raw response  
 ✔ Can get the last status code  

PHPExperts\RESTSpeaker\Tests\RESTAuth  
 ✔ Cannot build itself  
 ✔ Children can build themselves  
 ✔ Will not allow invalid auth modes  
 ✔ Can set a custom api client  
 ✔ Wont call a nonexisting auth strat  
 ✔ Supports no auth  
 ✔ Supports XAPI Token auth  
 ✔ Supports custom auth strategies  
 ✔ Has its own env() polyfill  
 ✔ Implements Guzzle's PSR-18 ClientInterface interface.  

PHPExperts\RESTSpeaker\Tests\RESTSpeaker  
 ✔ Can build itself  
 ✔ Returns null when no content  
 ✔ Returns exact unmodified data when not JSON 
 ✔ JSON URLs return plain PHP arrays  
 ✔ Can fall down to HTTPSpeaker  
 ✔ Requests the application/json Content-Type  
 ✔ Can get the last raw Guzzle response  
 ✔ Can get the last status code  
 ✔ Will automagically pass arrays as JSON via POST, PATCH and PUT.  
 ✔ Will automagically pass objects as JSON via POST, PATCH and PUT.  
 ✔ Implements Guzzle's PSR-18 ClientInterface interface.  

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

