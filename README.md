[![Latest Stable Version][latest stable version]][packagist]
 [![GitHub stars][github stars]][packagist]
 [![Total Downloads][total downloads]][packagist]
 [![GitHub Workflow Status][github workflow status]][github actions]
 [![Scrutinizer code quality][code quality]][code quality link]
 [![Type Coverage][type coverage]][sheperd type coverage]
 [![Code Coverage][code coverage]][code quality link]
 [![License][license]][packagist]
 [![Donate!][donate github]][github sponsor]
 [![Donate!][donate paypal]][paypal sponsor]

# Mock SOAP Client

A fake and predictable SOAP client ;-)

This library let you configure a SOAP client and the responses it returns.

Not any real SOAP requests are sent, the sole purpose of this library is for testing.

## Installation

```composer require loophp/mock-soapclient --dev```

## Usage

Using an array of responses

```php
<?php

include __DIR__ . '/vendor/autoload.php';

use loophp\MockSoapClient\MockSoapClient;

$responses = ['a', 'b', 'c'];

$client = new MockSoapClient($responses);

$client->foo();  // a
$client->bar();  // b
$client->w00t(); // c
$client->foobar(); // a
$client->barfoo(); // b
$client->plop();   // c
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

$client->foo();  // foo
$client->bar();  // bar
$client->w00t(); // w00t
$client->foobar(); // foobar
$client->barfoo(); // barfoo
$client->plop();   // plop
```

```php
<?php

declare(strict_types=1);

include __DIR__ . '/vendor/autoload.php';

use loophp\MockSoapClient\MockSoapClient;

$responses = static function ($method, $arguments) {
    switch ($method) {
        case 'foo':
            return 'foo_method';
        case 'bar':
            return 'bar_method';
    }

    throw new SoapFault('Server', sprintf('Unknown SOAP method "%s"', $method));
};

$client = new MockSoapClient($responses);

$client->foo();                  // foo_method
$client->__soapCall('foo', []);  // foo_method
$client->bar();                  // bar_method
$client->__soapCall('bar', []);  // bar_method
$client->w00t();                 // Throws exception SoapFault.
$client->__soapCall('w00t', []); // Throws exception SoapFault.
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

$client->foo();  // 00foo
$client->bar();  // 11bar
$client->w00t(); // SoapFault exception.
```

Advanced responses factory

```php
<?php

declare(strict_types=1);

include __DIR__ . '/vendor/autoload.php';

use loophp\MockSoapClient\MockSoapClient;

$responses = [
    'a',
    'b',
    'c',
    'a' => 'aaa',
    'b' => [
        'bbb1',
        'bbb2',
    ],
    'c' => [
        static function ($method, $arguments) {
            return 'ccc1';
        },
        static function ($method, $arguments) {
            return 'ccc2';
        },
    ],
];

$client = new MockSoapClient($responses);

$client->foo(); // a
$client->foo(); // b
$client->foo(); // c
$client->foo(); // a
$client->a(); // aaa
$client->a(); // aaa
$client->b(); // bbb1
$client->b(); // bbb2
$client->b(); // bbb1
$client->c(); // ccc1
$client->c(); // ccc2
$client->c(); // ccc1
```

## Code quality, tests and benchmarks

Every time changes are introduced into the library, [Github](https://github.com/loophp/mock-soapclient/actions) run the tests and the benchmarks.

The library has tests written with [PHPSpec](http://www.phpspec.net/).
Feel free to check them out in the `spec` directory. Run `composer phpspec` to trigger the tests.

Before each commit some inspections are executed with [GrumPHP](https://github.com/phpro/grumphp), run `./vendor/bin/grumphp run` to check manually.

[PHPInfection](https://github.com/infection/infection) is used to ensure that your code is properly tested, run `composer infection` to test your code.

## Contributing

Feel free to contribute to this library by sending Github pull requests.

If you can't contribute to the code, you can also sponsor me on [Github][github sponsor] or [Paypal][paypal sponsor].

## Changelog

See [CHANGELOG.md][changelog-md] for a changelog based on [git commits][git-commits].

For more detailed changelogs, please check [the release changelogs][changelog-releases].

[latest stable version]: https://img.shields.io/packagist/v/loophp/mock-soapclient.svg?style=flat-square
[packagist]: https://packagist.org/packages/loophp/mock-soapclient

[github stars]: https://img.shields.io/github/stars/loophp/mock-soapclient.svg?style=flat-square

[total downloads]: https://img.shields.io/packagist/dt/loophp/mock-soapclient.svg?style=flat-square

[github workflow status]: https://img.shields.io/github/workflow/status/loophp/mock-soapclient/Continuous%20Integration?style=flat-square
[github actions]: https://github.com/loophp/mock-soapclient/actions

[code quality]: https://img.shields.io/scrutinizer/quality/g/loophp/mock-soapclient/master.svg?style=flat-square
[code quality link]: https://scrutinizer-ci.com/g/loophp/mock-soapclient/?branch=master

[type coverage]: https://shepherd.dev/github/loophp/mock-soapclient/coverage.svg
[sheperd type coverage]: https://shepherd.dev/github/loophp/mock-soapclient

[code coverage]: https://img.shields.io/scrutinizer/coverage/g/loophp/mock-soapclient/master.svg?style=flat-square
[code quality link]: https://img.shields.io/scrutinizer/quality/g/loophp/mock-soapclient/master.svg?style=flat-square

[license]: https://img.shields.io/packagist/l/loophp/mock-soapclient.svg?style=flat-square

[donate github]: https://img.shields.io/badge/Sponsor-Github-brightgreen.svg?style=flat-square
[github sponsor]: https://github.com/sponsors/drupol

[donate paypal]: https://img.shields.io/badge/Sponsor-Paypal-brightgreen.svg?style=flat-square
[paypal sponsor]: https://www.paypal.me/drupol
