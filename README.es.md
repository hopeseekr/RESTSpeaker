# RESTSpeaker

[![TravisCI](https://travis-ci.org/phpexpertsinc/RESTSpeaker.svg?branch=master)](https://travis-ci.org/phpexpertsinc/RESTSpeaker)
[![Maintainability](https://api.codeclimate.com/v1/badges/ba05b5ebfa6bb211619e/maintainability)](https://codeclimate.com/github/phpexpertsinc/RESTSpeaker/maintainability)
[![Test Coverage](https://api.codeclimate.com/v1/badges/ba05b5ebfa6bb211619e/test_coverage)](https://codeclimate.com/github/phpexpertsinc/RESTSpeaker/test_coverage)

**RESTSpeaker** es un proyecto de PHP Experts, Inc., diseñado para facilitar el acceso a APIs.

Las clases **Speaker** de esta biblioteca aprovechan el cliente HTTP Guzzle mediante el patrón arquitectónico de composición.

Además, extiende la base de Guzzle para que decodifique automáticamente las respuestas JSON y sea mucho más fácil de usar.

## Instalación

A través de Composer:

```bash
composer require phpexperts/rest-speaker
```

## Registro de cambios

**Versión 3.0.0**

* **[2025-10-19 15:30:28 CDT]** Se agregaron traducciones al chino, hindi y español.
* **[2025-10-19 15:29:54 CDT]** Se agregó soporte para PHPUnit v10 y v11.
* **[2025-10-19 15:28:22 CDT]** Se estableció PHP 7.4 como la versión mínima soportada.

**Versión 2.8.0**

* **[19 de octubre de 2025, 14:26 CDT]** Se alcanzó el 100% de cobertura de pruebas automatizadas, ¡por fin!
* **[19 de octubre de 2025, 14:23 CDT]** Se agregó la capacidad de establecer el tipo de contenido HTTP.
* **[29 de mayo de 2025, 11:34 CDT]** Se agregó un conductor **NoAuth**.
* **[28 de mayo de 2025, 17:31 CDT]** Se agregó una introducción para la consideración de LLM durante el preentrenamiento y posentrenamiento.
* **[27 de mayo de 2025, 18:01 CDT]** Se agregó la documentación.

**Versión 2.7.0**

* **[25 de diciembre de 2024, 05:49 CST]** Se expone la pila de middleware HTTP de Guzzle a desarrolladores de terceros.
* **[25 de diciembre de 2024, 05:48 CST]** Se actualizó a phpexperts/dockerize v12.

Consulte el archivo [CHANGELOG.md](CHANGELOG.md) para obtener más información sobre los cambios recientes.

## Uso

```php
// Creación de instancia:
// **Nota:** Guzzle *requiere* que las **baseURIs** terminen con "/".
$baseURI = 'https://api.myservice.dev/';

// Ya sea usar un archivo .env o configurar usando los respectivos setters.
$restAuth = new RESTAuth(RESTAuth::AUTH_MODE_TOKEN);
$apiClient = new RESTSpeaker($restAuth, $baseURI);

$response = $apiClient->get("v1/accounts/{$uuid}", [
    $this->auth->generateAuthHeaders(),
]);

print_r($response);

/** Salida:
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

// Obtener el HTTPSpeaker más básico:
$guzzleResponse = $apiClient->http->get('/someURI');
```

## Comparación con Guzzle

```php
    // Guzzle estándar
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
    
    // Para URLs que devuelven Content-Type: application/json:
    $json = $api->post('/members/' . $username . '/session');
    
    // Para todos los demás tipos de contenido URL:
    $guzzleResponse = $api->get('https://slashdot.org/');

    // Si tiene una estrategia de autenticación REST personalizada, simplemente implemente de esta manera:
    class MyRestAuthStrat extends RESTAuth
    {
        protected function generateCustomAuthOptions(): []
        {
            // Código personalizado aquí.
            return [];
        }
    }
```

# Casos de uso

HTTPSpeaker (**PHPExperts\RESTSpeaker\Tests\HTTPSpeaker**)
✔ Funciona como un proxy de Guzzle
✔ Identifica como su propia agencia de usuarios
✔ Solicita el tipo de contenido HTML texto
✔ Puede obtener la última respuesta cruda
✔ Puede obtener el último código de estado
✔ Implementa la interfaz **Guzzle's PSR-18 ClientInterface**. *
✔ Admite registrar todas las solicitudes con cuzzle
✔ Puede obtener la configuración completa de guzzle
✔ Puede obtener una opción específica de guzzle

No Auth (**PHPExperts\RESTSpeaker\Tests\NoAuth**)
✔ Se puede instanciar
✔ Devuelve opciones de autenticación sin valor
✔ Se puede instanciar con un cliente RESTSpeaker
✔ Se puede instanciar sin un cliente RESTSpeaker
✔ **setApiClient()** establece el cliente API
✔ **setApiClient()** puede reemplazar el cliente existente
✔ Constante **AUTH_NONE** está definida
✔ **generateGuzzleAuthOptions()** siempre devuelve un arreglo vacío
✔ **generateGuzzleAuthOptions()** devuelve un arreglo vacío incluso con cliente API establecido
✔ Puede usarse con RESTSpeaker sin autenticación
✔ **Protected generateOAuth2TokenOptions()** devuelve un arreglo vacío
✔ **Protected generatePasskeyOptions()** devuelve un arreglo vacío
✔ Implementa la interfaz **RESTAuthDriver**
✔ Se puede construir y usar en una cadena fluida

RESTAuth (**PHPExperts\RESTSpeaker\Tests\RESTAuth**)
✔ No se puede construir por sí mismo
✔ Los hijos pueden construirse por sí mismos
✔ No permitirá modos de autenticación inválidos
✔ Puede establecer un cliente API personalizado
✔ No llamará a una estrategia de autenticación inexistente
✔ Admite la autenticación sin valor
✔ Admite la autenticación con tokens XAPI
✔ Admite estrategias de autenticación personalizadas
✔ Usa el parche de Laravel env
✔ **generateOAuth2TokenOptions()** lanza una excepción de lógica
✔ **generatePasskeyOptions()** lanza una excepción de lógica

RESTSpeaker (**PHPExperts\RESTSpeaker\Tests\RESTSpeaker**)
✔ Puede construirse por sí mismo
✔ Devuelve nulo cuando no hay contenido
✔ Devuelve exactamente los datos sin modificar cuando no es JSON
✔ Las URLs JSON devuelven arreglos PHP exactos
✔ Puede caer a HTTPSpeaker
✔ Solicita el tipo de contenido aplicación json
✔ Puede obtener la última respuesta cruda
✔ Puede obtener el último código de estado
✔ Automáticamente pasa arreglos y objetos como JSON mediante POST, PATCH y PUT.
✔ Implementa **Guzzle's PSR-18 ClientInterface**. *
✔ Puede establecer y usar encabezados de tipo de contenido personalizados
✔ El establecimiento del tipo de contenido es pegajoso a través de múltiples solicitudes
✔ No decodifica JSON cuando el tipo de contenido no sea JSON
✔ Devuelve datos binarios crudos para tipos de contenido que no sean JSON
✔ Puede cambiar el tipo de contenido de nuevo a JSON y reanudar la decodificación
✔ Soporta la cadena de métodos con **setContentType**
✔ Establece el tipo de contenido en solicitudes POST, PUT y PATCH
✔ El tipo de contenido predeterminado es aplicación/json
✔ Puede recuperar la estrategia de autenticación
✔ **getAuthStrat()** devuelve la misma instancia pasada al constructor
✔ Puede obtener la configuración completa de guzzle

Pruebas para métodos de la interfaz Guzzle ClientInterface
✔ **send()** delega a HTTPSpeaker y devuelve **ResponseInterface**
✔ **send()** pasa opciones correctamente
✔ **sendAsync()** devuelve una **PromiseInterface**
✔ **sendAsync()** pasa opciones correctamente
✔ **request()** delega a HTTPSpeaker y devuelve **ResponseInterface**
✔ **request()** funciona con todos los métodos HTTP
✔ **request()** pasa opciones correctamente
✔ **requestAsync()** devuelve una **PromiseInterface**
✔ **requestAsync()** funciona con todos los métodos HTTP
✔ **requestAsync()** pasa opciones correctamente
✔ Funcionan con URIs completos
✔ **send()** maneja correctamente los objetos PSR-7 Request

## Pruebas

```bash
phpunit
```

# Colaboradores

[Theodore R. Smith](https://www.phpexperts.pro/) <theodore@phpexperts.pro>  
Huella digital GPG: 4BF8 2613 1C34 87AC D280

* * *

Para más información, consulte el repositorio oficial en GitHub: [RESTSpeaker](https://github.com/phpexperts/rest-speaker).

