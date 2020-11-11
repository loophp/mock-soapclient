<?php

declare(strict_types=1);

namespace loophp\MockSoapClient;

use ArrayIterator;
use InfiniteIterator;
use SoapClient;
use SoapFault;
use SoapHeader;

use function array_key_exists;
use function func_get_args;
use function is_callable;

use const ARRAY_FILTER_USE_KEY;

class MockSoapClient extends SoapClient
{
    /**
     * @var array<int|string, InfiniteIterator>
     */
    private $iterators;

    /**
     * MockSoapClient constructor.
     *
     * @param mixed $responses
     */
    public function __construct($responses)
    {
        $this->iterators = $this->buildIterators((array) $responses);
    }

    /**
     * @param string $function_name
     * @param array<mixed> $arguments
     *
     * @throws SoapFault
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
        $iterator = true === array_key_exists($function_name, $this->iterators) ?
            $this->iterators[$function_name] :
            $this->iterators['*'];

        $response = $iterator->current();
        $iterator->next();

        if ($response instanceof SoapFault) {
            throw $response;
        }

        return true === is_callable($response) ?
            ($response)(...func_get_args()) :
            $response;
    }

    /**
     * Build a simple Infinite iterator.
     *
     * @param array<mixed> $data
     */
    private function buildIterator(array $data): InfiniteIterator
    {
        $iterator = new InfiniteIterator(new ArrayIterator($data));
        $iterator->rewind();

        return $iterator;
    }

    /**
     * Build the structure of iterators.
     *
     * @param array<callable|mixed> $data
     *
     * @return array<int|string, InfiniteIterator>
     */
    private function buildIterators(array $data): array
    {
        return array_reduce(
            array_keys($data),
            /**
             * @param int|string $key
             *
             * @return array<int|string, InfiniteIterator>
             */
            function (array $iterators, $key) use ($data): array {
                if (false === is_numeric($key)) {
                    $iterators[$key] = $this->buildIterator((array) $data[$key]);
                }

                return $iterators;
            },
            [
                '*' => $this->buildIterator(array_filter($data, 'is_numeric', ARRAY_FILTER_USE_KEY)),
            ]
        );
    }
}
