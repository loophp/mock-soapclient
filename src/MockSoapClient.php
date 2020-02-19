<?php

declare(strict_types=1);

namespace loophp\MockSoapClient;

use InvalidArgumentException;
use SoapClient;
use SoapFault;
use SoapHeader;

use function array_key_exists;
use function count;
use function func_get_args;
use function is_callable;

use const ARRAY_FILTER_USE_KEY;

/**
 * Class MockSoapClient.
 */
class MockSoapClient extends SoapClient
{
    /**
     * @var array<string, int>
     */
    private $indexes;

    /**
     * @var array<mixed>
     */
    private $responses;

    /**
     * MockSoapClient constructor.
     *
     * @param array<mixed>|callable $responses
     */
    public function __construct($responses = null)
    {
        $responses = (array) $responses;

        if ([] === $responses) {
            throw new InvalidArgumentException('The response argument cannot be empty.');
        }

        $this->responses = $responses;
        $this->indexes = [
            '*' => 0,
        ];
    }

    /**
     * @param string $function_name
     * @param array<mixed> $arguments
     *
     * @throws \SoapFault
     *
     * @return mixed
     */
    public function __call($function_name, $arguments = [])
    {
        try {
            $response = $this->__soapCall($function_name, $arguments);
        } catch (SoapFault $exception) {
            throw $exception;
        }

        return $response;
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
        if (false === array_key_exists($function_name, $this->indexes)) {
            $this->indexes[$function_name] = 0;
        }

        $index = &$this->indexes['*'];
        $responses = $this->responses;

        if (true === array_key_exists($function_name, $responses)) {
            $responses = (array) $responses[$function_name];
            $index = &$this->indexes[$function_name];
        }

        $index %= count(
            array_filter(
                $responses,
                static function (string $key) {
                    return is_numeric($key);
                },
                ARRAY_FILTER_USE_KEY
            )
        );

        $response = $responses[$index];

        if (is_callable($response)) {
            ++$index;

            return ($response)(...func_get_args());
        }

        if ($response instanceof SoapFault) {
            throw $response;
        }

        ++$index;

        return $response;
    }
}
