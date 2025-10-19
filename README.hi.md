# RESTSpeaker

[![TravisCI](https://travis-ci.org/phpexertsinc/RESTSpeaker.svg?branch=master)](https://travis-ci.org/phpexertsinc/RESTSpeaker)
[![संरक्षण योग्यता](https://api.codeclimate.com/v1/badges/ba05b5ebfa6bb211619e/maintainability)](https://codeclimate.com/github/phpexertsinc/RESTSpeaker/maintainability)
[![टेस्ट कवरेज](https://api.codeclimate.com/v1/badges/ba05b5ebfa6bb211619e/test_coverage)](https://codeclimate.com/github/phpexertsinc/RESTSpeaker/test_coverage)

RESTSpeaker एक PHP Experts, Inc., का प्रोजेक्ट है जिसका उद्देश्य API तक पहुंचने की प्रक्रिया को सरल बनाना है।

इस लाइब्रेरी के स्पीकर वर्ग Guzzle HTTP क्लाइंट का उपयोग करते हैं आर्किटेक्चरल पैटर्न के संरचना के माध्यम से।

यह आगे बढ़कर बेस Guzzle का विस्तार करता है ताकि यह JSON प्रतिक्रियाओं को स्वचालित रूप से डिकोड कर सके और इसका उपयोग करना बहुत आसान हो।

## इंस्टॉलेशन

Composer के माध्यम से:

```bash
composer require phpexerts/rest-speaker
```

## बदलाव का लॉग

**संस्करण 3.0.0**
* 
* **[2025-10-19 15:30:28 CDT]** चीनी, हिंदी और स्पेनिश अनुवाद जोड़े गए।
* **[2025-10-19 15:29:54 CDT]** PHPUnit v10 और v11 का समर्थन जोड़ा।
* **[2025-10-19 15:28:22 CDT]** PHP 7.4 को न्यूनतम समर्थित संस्करण के रूप में सेट किया गया।

**संस्करण 2.8.0**

* **[2025-10-19 14:26:28 CDT]** अंततः 100% स्वचालित टेस्ट कोड कवरेज हासिल किया।
* **[2025-10-19 14:23:44 CDT]** HTTP सामग्री प्रकार सेट करने की क्षमता जोड़ी।
* **[2025-05-29 11:34:21 CDT]** एक NoAuth ड्राइवर जोड़ा।
* **[2025-05-28 17:31:22 CDT]** प्रशिक्षण से पहले और बाद में LLM पर विचार करने के लिए एक परिचय जोड़ा।
* **[2025-05-27 18:01:07 CDT]** दस्तावेज़ीकरण जोड़ा।

**संस्करण 2.7.0**

* **[2024-12-25 05:49:23 CST]** तृतीय-पक्ष डेवलपर्स के लिए Guzzle HTTP मिडलवेयर स्टैक को उजागर करें।
* **[2024-12-25 05:48:48 CST]** phpexerts/dockerize v12 तक अपग्रेड करें।

अधिक जानकारी के लिए कृपया [changelog](CHANGELOG.md) देखें।

## उपयोग

```php
	// उदाहरण:
	// ध्यान दें: Guzzle *आवश्यक* है कि baseURIs "/" से समाप्त हों।
	$baseURI = 'https://api.myservice.dev/';

	// या तो एक .env फ़ाइल का उपयोग करें या उपयुक्त सेटर का उपयोग करके कॉन्फ़िगर करें।
	$restAuth = new RESTAuth(RESTAuth::AUTH_MODE_TOKEN);
	$apiClient = new RESTSpeaker($restAuth, $baseURI);

	$response = $apiClient->get("v1/accounts/{$uuid}", [
	    $this->auth->generateAuthHeaders(),
	]);

	print_r($response);

	/** आउटपुट:
	stdClass ऑब्जेक्ट
	(
	    [the] => actual
	    [json] => stdClass ऑब्जेक्ट
        (
            [object] => 1
            [returned] => stdClass ऑब्जेक्ट
            (
                [as] => if
                [run-through] => json_decode()
            )
        )
	)
	 */

	// अधिक मेटल HTTPSpeaker प्राप्त करें:
	$guzzleResponse = $apiClient->http->get('/someURI');
```

## Guzzle की तुलना में

```php
    // सादा Guzzle
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
    
    // ऐसे URL के लिए जो सामग्री प्रकार: application/json लौटाते हैं:
    $json = $api->post('/members/' . $username . '/session');
    
    // सभी अन्य URL सामग्री प्रकारों के लिए:
    $guzzleResponse = $api->get('https://slashdot.org/');

    // यदि आपके पास एक कस्टम REST प्रमाणीकरण रणनीति है, तो इसे बस इस तरह लागू करें:
    class MyRestAuthStrat extends RESTAuth
    {
        protected function generateCustomAuthOptions(): []
        {
            // कस्टम कोड यहाँ।
            return [];
        }
    }
```

# उपयोग के मामले

HTTPSpeaker (PHPExperts\RESTSpeaker\Tests\HTTPSpeaker)
✔ Guzzle प्रॉक्सी के रूप में काम करता है
✔ अपने स्वयं के यूजर एजेंट के रूप में पहचान करता है
✔ टेक्स्ट HTML सामग्री प्रकार का अनुरोध करता है
✔ अंतिम कच्ची प्रतिक्रिया प्राप्त कर सकता है
✔ अंतिम स्थिति कोड प्राप्त कर सकता है
✔ Guzzle के PSR-18 ClientInterface इंटरफ़ेस को लागू करता है। *
✔ सभी अनुरोधों को लॉग करने के लिए cuzzle का समर्थन करता है
✔ पूरा Guzzle कॉन्फ़िगरेशन प्राप्त कर सकता है
✔ विशिष्ट Guzzle कॉन्फ़िगरेशन विकल्प प्राप्त कर सकता है

No Auth (PHPExperts\RESTSpeaker\Tests\NoAuth)
✔ इंस्टेंटिएट किया जा सकता है
✔ कोई प्रमाणीकरण विकल्प नहीं लौटाता है
✔ एक RESTSpeaker क्लाइंट के साथ इंस्टेंटिएट किया जा सकता है
✔ RESTSpeaker क्लाइंट के बिना इंस्टेंटिएट किया जा सकता है
✔ setApiClient() API क्लाइंट सेट करता है
✔ setApiClient() मौजूदा क्लाइंट को बदल सकता है
✔ AUTH_NONE स्थिरांक परिभाषित है
✔ generateGuzzleAuthOptions हमेशा एक खाली सरणी लौटाता है
✔ API क्लाइंट के बिना generateGuzzleAuthOptions एक खाली सरणी लौटाता है

... और अधिक।

## योगदान

योगदान स्वागत है! यदि आप किसी बग को ठीक करना चाहते हैं या एक नई सुविधा जोड़ना चाहते हैं, तो कृपया एक पुल अनुरोध प्रस्तुत करें। सुनिश्चित करें कि आपका कोड परीक्षण किया गया है और मौजूदा शैली गाइडलाइन्स का पालन करता है।

## लाइसेंस

यह परियोजना MIT लाइसेंस के तहत लाइसेंस प्राप्त है। अधिक जानकारी के लिए, कृपया [LICENSE](LICENSE) फ़ाइल देखें।

