<?php

declare(strict_types=1);

namespace spec\loophp\MockSoapClient;

use loophp\MockSoapClient\MockSoapClient;
use PhpSpec\ObjectBehavior;

class MockSoapClientSpec extends ObjectBehavior
{
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
}
