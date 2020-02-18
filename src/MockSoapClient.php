<?php

declare(strict_types=1);

namespace loophp\MockSoapClient;

use SoapClient;
use SoapFault;
use SoapHeader;

use function array_key_exists;
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
     * @var array<mixed>|callable|null
     */
    private $responses;

    /**
     * MockSoapClient constructor.
     *
     * @param array<mixed>|callable $responses
     */
    public function __construct($responses = null)
    {
        $this->responses = $responses;
        $this->currentIndex = -1;
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
        $index = ++$this->currentIndex;

        if (false === is_array($this->responses) && false === is_callable($this->responses)) {
            throw new SoapFault('Server', 'Invalid mock response format');
        }

        $responses = $this->responses;

        if (is_callable($responses)) {
            return ($responses)(...func_get_args());
        }

        $index %= count($responses);

        if (!array_key_exists($index, $responses)) {
            throw new SoapFault('Server', 'No more mock responses.');
        }

        $response = $responses[$index];

        if ($response instanceof SoapFault) {
            throw $response;
        }

        return $response;
    }
}
