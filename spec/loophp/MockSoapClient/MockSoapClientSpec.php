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

    public function it_is_able_to_handle_exception()
    {
        $responses = [
            new SoapFault('Server', 'foo'),
        ];

        $this->beConstructedWith($responses);

        $this
            ->shouldThrow(SoapFault::class)
            ->during('__soapCall', ['foo', []]);
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
