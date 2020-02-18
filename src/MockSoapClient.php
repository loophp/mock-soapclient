<?php

declare(strict_types=1);

namespace loophp\MockSoapClient;

use InvalidArgumentException;
use SoapClient;
use SoapFault;
use SoapHeader;

use function count;
use function func_get_args;
use function is_array;
use function is_callable;

/**
 * Class MockSoapClient.
 */
class MockSoapClient extends SoapClient
{
    /**
     * @var int
     */
    private $currentIndex;

    /**
     * @var array<mixed>|callable
     */
    private $responses;

    /**
     * MockSoapClient constructor.
     *
     * @param array<mixed>|callable $responses
     */
    public function __construct($responses = null)
    {
        if (false === is_array($responses) && false === is_callable($responses)) {
            throw new InvalidArgumentException('The response argument must be an array or a callable.');
        }

        $this->responses = $responses;
        $this->currentIndex = 0;
    }

    /**
     * @param string $function_name
     * @param array<mixed> $arguments
     * @param array<mixed>|null $options
     * @param array<mixed>|SoapHeader|null $input_headers
     * @param array<mixed>|null $output_headers
     *
     * @throws SoapFault
     *
     * @return mixed
     */
    public function __soapCall(
        $function_name,
        $arguments,
        $options = null,
        $input_headers = null,
        &$output_headers = null
    ) {
        $index = $this->currentIndex++;

        $responses = $this->responses;

        if (is_callable($responses)) {
            return ($responses)(...func_get_args());
        }

        $index %= count($responses);

        $response = $responses[$index];

        if (is_callable($response)) {
            return ($response)(...func_get_args());
        }

        if ($response instanceof SoapFault) {
            throw $response;
        }

        return $response;
    }
}
