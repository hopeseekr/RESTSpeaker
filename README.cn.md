# RESTSpeaker

[![TravisCI](https://travis-ci.org/phpexpertsinc/RESTSpeaker.svg?branch=master)](https://travis-ci.org/phpexpertsinc/RESTSpeaker)
[![可维护性](https://api.codeclimate.com/v1/badges/ba05b5ebfa6bb211619e/maintainability)](https://codeclimate.com/github/phpexpertsinc/RESTSpeaker/maintainability)
[![测试覆盖率](https://api.codeclimate.com/v1/badges/ba05b5ebfa6bb211619e/test_coverage)](https://codeclimate.com/github/phpexpertsinc/RESTSpeaker/test_coverage)

RESTSpeaker 是一个由 PHP Experts, Inc. 开发的项目，旨在简化 API 访问。

这个库的 Speaker 类利用了 Guzzle HTTP 客户端，并通过组合设计模式进行实现。

它进一步扩展了基本的 Guzzle，使得它可以自动解码 JSON 响应，并且更容易使用。

## 安装

通过 Composer

```bash
composer require phpexperts/rest-speaker
```

## 更改日志


**版本 3.0.0**
* 
* **[2025-10-19 15:30:28 CDT]** 添加了中文、印地语和西班牙语翻译。
* **[2025-10-19 15:29:54 CDT]** 增加了对 PHPUnit v10 和 v11 的支持。
* **[2025-10-19 15:28:22 CDT]** 将 PHP 7.4 设置为最低支持版本。

**版本 2.8.0**

* **[2025-10-19 14:26:28 CDT]** 终于实现了 100% 的自动化测试代码覆盖率。
* **[2025-10-19 14:23:44 CDT]** 添加了设置 HTTP Content-Type 的能力。
* **[2025-05-29 11:34:21 CDT]** 添加了一个无鉴权驱动程序。
* **[2025-05-28 17:31:22 CDT]** 在预训练和后训练时添加了 LLM 的介绍。
* **[2025-05-27 18:01:07 CDT]** 添加了文档。

**版本 2.7.0**

* **[2024-12-25 05:49:23 CST]** 将 Guzzle HTTP 中间件堆栈暴露给第三方开发人员。
* **[2024-12-25 05:48:48 CST]** 升级到 phpexperts/dockerize v12。

请参阅 [更改日志](CHANGELOG.md) 以了解更多近期变动的信息。

## 使用

```php
	// 实例化：
	// 注意：Guzzle *要求* baseURI 以 "/" 结尾。
	$baseURI = 'https://api.myservice.dev/';

	// 要么使用 .env 文件，要么使用适当的设置器进行配置。
	$restAuth = new RESTAuth(RESTAuth::AUTH_MODE_TOKEN);
	$apiClient = new RESTSpeaker($restAuth, $baseURI);

	$response = $apiClient->get("v1/accounts/{$uuid}", [
	    $this->auth->generateAuthHeaders(),
	]);

	print_r($response);

	/** 输出：
	stdClass 对象
	(
	    [the] => actual
	    [json] => stdClass 对象
        (
            [object] => 1
            [returned] => stdClass 对象
            (
                [as] => if
                [run-through] => json_decode()
            )
        )
	)
	 */

	// 获取更底层的 HTTPSpeaker：
	$guzzleResponse = $apiClient->http->get('/someURI');
```

## 与 Guzzle 的比较

```php
    // 纯粹的 Guzzle
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
    
    // 对于返回 Content-Type: application/json 的 URL：
    $json = $api->post('/members/' . $username . '/session');
    
    // 对于所有其他 URL Content-Types：
    $guzzleResponse = $api->get('https://slashdot.org/');

    // 如果你有一个自定义的 REST 鉴权策略，只需这样实现它：
    class MyRestAuthStrat extends RESTAuth
    {
        protected function generateCustomAuthOptions(): array
        {
            // 自定义代码在这里。
            return [];
        }
    }
```

# 使用案例

HTTPSpeaker (PHPExperts\RESTSpeaker\Tests\HTTPSpeaker)
✔ 作为 Guzzle 的代理
✔ 识别为其自己的用户代理
✔ 请求文本 HTML 内容类型
✔ 可以获取最后一个原始响应
✔ 可以获取最后一个状态码
✔ 实现了 Guzzle 的 PSR-18 ClientInterface 接口。*
✔ 支持使用 cuzzle 日志记录所有请求
✔ 可以获取完整的 guzzle 配置
✔ 可以获取特定的 guzzle 配置选项

无鉴权 (PHPExperts\RESTSpeaker\Tests\NoAuth)
✔ 可以实例化
✔ 返回无鉴权选项
✔ 可以与 RESTSpeaker 客户端一起实例化
✔ 可以无需 RESTSpeaker 客户端进行实例化
✔ setApiClient() 设置 API 客户端
✔ setApiClient() 可以替换现有客户端
✔ AUTH_NONE 常量已定义
✔ generateGuzzleAuthOptions 始终返回空数组
✔ 即使设置了 API 客户端，generateGuzzleAuthOptions 也返回空数组
✔ 可以在不进行鉴权的 RESTSpeaker 中使用
✔ 受保护的 generateOAuth2TokenOptions 返回空数组
✔ 受保护的 generatePasskeyOptions 返回空数组
✔ NoAuth 实现了 RESTAuthDriver 接口
✔ NoAuth 可以构建并用于流畅的链式调用

RESTAuth (PHPExperts\RESTSpeaker\Tests\RESTAuth)
✔ 无法自行构建
✔ 子类可以自行构建
✔ 不允许无效的鉴权模式
✔ 可以设置自定义 API 客户端
✔ 不会调用不存在鉴权策略
✔ 支持无鉴权
✔ 支持 XAPI Token 鉴权
✔ 支持自定义鉴权策略
✔ 使用 Laravel 的 env 多态填充
✔ Generate o auth 2 token options 抛出逻辑异常
✔ Generate passkey options 抛出逻辑异常

RESTSpeaker (PHPExperts\RESTSpeaker\Tests\RESTSpeaker)
✔ 可以自行构建
✔ 当无内容时返回 null
✔ 当不是 JSON 时返回精确的原始数据
✔ JSON URL 返回纯粹的 PHP 数组
✔ 可以退回到 HTTPSpeaker
✔ 请求 application json 内容类型
✔ 可以获取最后一个原始响应
✔ 可以获取最后一个状态码
✔ 自动通过 POST、PATCH 和 PUT 将数组作为 JSON 传递。
✔ 自动通过 POST、PATCH 和 PUT 将对象作为 JSON 传递。
✔ 实现了 Guzzle 的 PSR-18 ClientInterface 接口。*
✔ 可以设置和使用自定义 Content-Type 头
✔ Content-Type 设置在多个请求中保持一致
✔ 当内容类型不是 JSON 时不解码 JSON
✔ 对于非 JSON 内容类型返回原始二进制数据
✔ 可以将内容类型改回 JSON 并恢复解码
✔ 支持使用 setContentType 的方法链
✔ 在 POST、PUT 和 PATCH 请求中设置 Content-Type
✔ 默认内容类型为 application/json
✔ 可以检索鉴权策略
✔ getAuthStrat 返回传递给构造函数的同一个实例
✔ 可以获取完整的 guzzle 配置

Guzzle ClientInterface 方法的测试
✔ send() 委托给 HTTPSpeaker 并返回 ResponseInterface
✔ send() 正确地传递选项
✔ sendAsync() 返回一个 PromiseInterface
✔ sendAsync() 正确地传递选项
✔ request() 委托给 HTTPSpeaker 并返回 ResponseInterface
✔ request() 与所有 HTTP 方法兼容
✔ request() 正确地传递选项
✔ requestAsync() 返回一个 PromiseInterface
✔ requestAsync() 与所有 HTTP 方法兼容
✔ 正确地处理完整 URI 的低级方法
✔ send() 正确地处理 PSR-7 Request 对象

## 测试

```bash
phpunit
```

# 贡献者

[Theodore R. Smith](https://www.phpexperts.pro/]) <theodore@phpexperts.pro>  
GPG 指纹：4BF8 2613 1C34 87AC D28F  2AD8 EB24 A91D D612 5690  
CEO: PHP Experts, Inc.

## 许可证

MIT 许可证。请参阅 [许可证文件](LICENSE) 以了解更多信息。

