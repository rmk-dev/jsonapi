<?php

namespace Rmk\JsonApi\Document\ValueObject;

use JsonSerializable;

/**
 * Error source presentation
 */
class ErrorSource implements JsonSerializable
{

    /**
     * A JSON Pointer [RFC6901] to the associated entity in the request document
     *
     * @var string
     */
    private string $pointer;

    /**
     * A string indicating which URI query parameter caused the error.
     *
     * @var string
     */
    private string $parameter;

    /**
     * @param string $pointer
     * @param string $parameter
     */
    public function __construct(string $pointer = '', string $parameter = '')
    {
        $this->pointer = $pointer;
        $this->parameter = $parameter;
    }

    /**
     * @return string
     */
    public function getPointer(): string
    {
        return $this->pointer;
    }

    /**
     * @return string
     */
    public function getParameter(): string
    {
        return $this->parameter;
    }

    /**
     * @return mixed|string[]
     */
    public function jsonSerialize()
    {
        return array_filter([
            'pointer' => $this->getPointer(),
            'parameter' => $this->getParameter(),
        ], function($el) { return !empty($el); });
    }
}
