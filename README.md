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

$response = $client->__soapCall('foo', []); // a
$response = $client->__soapCall('bar', []); // b
$response = $client->__soapCall('w00t', []); // c
$response = $client->__soapCall('foobar', []); // a
$response = $client->__soapCall('barfoo', []); // b
$response = $client->__soapCall('plop', []); // c
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

$response = $client->__soapCall('foo', []); // foo
$response = $client->__soapCall('bar', []); // bar
$response = $client->__soapCall('w00t', []); // w00t
$response = $client->__soapCall('foobar', []); // foobar
$response = $client->__soapCall('barfoo', []); // barfoo
$response = $client->__soapCall('plop', []); // plop
```
