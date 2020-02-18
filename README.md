[![Latest Stable Version](https://img.shields.io/packagist/v/loophp/mock-soapclient.svg?style=flat-square)](https://packagist.org/packages/loophp/mock-soapclient)
 [![GitHub stars](https://img.shields.io/github/stars/loophp/mock-soapclient.svg?style=flat-square)](https://packagist.org/packages/loophp/mock-soapclient)
 [![Total Downloads](https://img.shields.io/packagist/dt/loophp/mock-soapclient.svg?style=flat-square)](https://packagist.org/packages/loophp/mock-soapclient)
 [![GitHub Workflow Status](https://img.shields.io/github/workflow/status/loophp/mock-soapclient/Continuous%20Integration?style=flat-square)](https://github.com/loophp/mock-soapclient/actions)
 [![Scrutinizer code quality](https://img.shields.io/scrutinizer/quality/g/loophp/mock-soapclient/master.svg?style=flat-square)](https://scrutinizer-ci.com/g/loophp/mock-soapclient/?branch=master)
 [![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/loophp/mock-soapclient/master.svg?style=flat-square)](https://scrutinizer-ci.com/g/loophp/mock-soapclient/?branch=master)
 [![Type Coverage](https://shepherd.dev/github/loophp/mock-soapclient/coverage.svg)](https://shepherd.dev/github/loophp/mock-soapclient)
 [![License](https://img.shields.io/packagist/l/loophp/mock-soapclient.svg?style=flat-square)](https://packagist.org/packages/loophp/mock-soapclient)
 [![Donate!](https://img.shields.io/badge/Donate-Paypal-brightgreen.svg?style=flat-square)](https://paypal.me/drupol)
 
# Mock SOAP Client

Mock a SOAP client.

Pretty useful for testing.

## Installation

```composer require loophp/mock-soapclient```

## Usage

Using an array of responses

```php
<?php

include __DIR__ . '/vendor/autoload.php';

use loophp\MockSoapClient\MockSoapClient;

$responses = ['a', 'b', 'c'];

$client = new MockSoapClient($responses);

$client->__soapCall('foo', []); // a
$client->__soapCall('bar', []); // b
$client->__soapCall('w00t', []); // c
$client->__soapCall('foobar', []); // a
$client->__soapCall('barfoo', []); // b
$client->__soapCall('plop', []); // c
```

Or using a closure

```php
<?php

include __DIR__ . '/vendor/autoload.php';

use loophp\MockSoapClient\MockSoapClient;

$responses = static function ($method, $arguments) {
    return $method;
};

$client = new MockSoapClient($responses);

$client->__soapCall('foo', []); // foo
$client->__soapCall('bar', []); // bar
$client->__soapCall('w00t', []); // w00t
$client->__soapCall('foobar', []); // foobar
$client->__soapCall('barfoo', []); // barfoo
$client->__soapCall('plop', []); // plop
```

Or using multiple closures

```php
<?php

include __DIR__ . '/vendor/autoload.php';

use loophp\MockSoapClient\MockSoapClient;

$responses = [
    static function (string $method, array $arguments) {
        return '00' . $method;
    },
    static function (string $method, array $arguments) {
        return '11' . $method;
    },
    static function (string $method, array $arguments) {
        throw new SoapFault('Server', 'Server');
    },
];

$client = new MockSoapClient($responses);

$client->__soapCall('foo', []); // 00foo
$client->__soapCall('bar', []); // 11bar
$client->__soapCall('w00t', []); // SoapFault exception.
```

## Code quality, tests and benchmarks

Every time changes are introduced into the library, [Github](https://github.com/loophp/mock-soapclient/actions) run the tests and the benchmarks.

The library has tests written with [PHPSpec](http://www.phpspec.net/).
Feel free to check them out in the `spec` directory. Run `composer phpspec` to trigger the tests.

Before each commit some inspections are executed with [GrumPHP](https://github.com/phpro/grumphp), run `./vendor/bin/grumphp run` to check manually.

[PHPInfection](https://github.com/infection/infection) is used to ensure that your code is properly tested, run `composer infection` to test your code.

## Contributing

Feel free to contribute to this library by sending Github pull requests.
