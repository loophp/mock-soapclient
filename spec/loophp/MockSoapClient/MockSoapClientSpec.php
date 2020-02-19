<?php

declare(strict_types=1);

namespace spec\loophp\MockSoapClient;

use InvalidArgumentException;
use loophp\MockSoapClient\MockSoapClient;
use PhpSpec\ObjectBehavior;
use SoapFault;
use stdClass;

class MockSoapClientSpec extends ObjectBehavior
{
    public function it_can_handle_an_array_of_responses_as_callable()
    {
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

        $this->beConstructedWith($responses);

        $this
            ->__soapCall('foo', [])
            ->shouldReturn('00foo');

        $this
            ->__soapCall('foo', [])
            ->shouldReturn('11foo');

        $this
            ->shouldThrow(SoapFault::class)
            ->during('__soapCall', ['foo', []]);
    }

    public function it_can_handle_complex_structure_of_responses()
    {
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

        $this->beConstructedWith($responses);

        $this
            ->foo()
            ->shouldReturn('a');
        $this
            ->foo()
            ->shouldReturn('b');
        $this
            ->foo()
            ->shouldReturn('c');
        $this
            ->a()
            ->shouldReturn('aaa');
        $this
            ->a()
            ->shouldReturn('aaa');
        $this
            ->b()
            ->shouldReturn('bbb1');
        $this
            ->b()
            ->shouldReturn('bbb2');
        $this
            ->b()
            ->shouldReturn('bbb1');
        $this
            ->c()
            ->shouldReturn('ccc1');
        $this
            ->c()
            ->shouldReturn('ccc2');
        $this
            ->c()
            ->shouldReturn('ccc1');
        $this
            ->foo()
            ->shouldReturn('a');
    }

    public function it_can_use_a_simple_callable_as_response()
    {
        $responses = static function ($method, $arguments) {
            return $method;
        };

        $this->beConstructedWith($responses);

        $this
            ->foo()
            ->shouldReturn('foo');
    }

    public function it_foo()
    {
        $this
            ->shouldThrow(InvalidArgumentException::class)
            ->during('__construct', [null]);
    }

    public function it_is_able_to_handle_exception()
    {
        $responses = [
            new SoapFault('Server', 'foo'),
        ];

        $this->beConstructedWith($responses);

        $this
            ->shouldThrow(SoapFault::class)
            ->during('__soapCall', ['foo', []]);

        $this
            ->shouldThrow(SoapFault::class)
            ->during('__call', ['foo', []]);
    }

    public function it_is_able_to_mock_soap_calls_with_an_array_of_responses()
    {
        $responses = ['a', 'b', 'c'];

        $this->beConstructedWith($responses);

        $this
            ->__soapCall('foo', [])
            ->shouldReturn('a');

        $this
            ->__soapCall('foo', [])
            ->shouldReturn('b');

        $this
            ->__soapCall('foo', [])
            ->shouldReturn('c');

        $this
            ->__soapCall('foo', [])
            ->shouldReturn('a');
    }

    public function it_is_able_to_mock_soap_calls_with_an_callable_of_responses()
    {
        $responses = static function ($method, $arguments) {
            return $method;
        };

        $this->beConstructedWith($responses);

        $this
            ->__soapCall('foo', [])
            ->shouldReturn('foo');

        $this
            ->__soapCall('bar', [])
            ->shouldReturn('bar');
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(MockSoapClient::class);
    }

    public function it_only_accept_responses_as_array_or_callable()
    {
        $responses = new stdClass();

        $this
            ->shouldThrow(InvalidArgumentException::class)
            ->during('__construct', [$responses]);
    }
}
