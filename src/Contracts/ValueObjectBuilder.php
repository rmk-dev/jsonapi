<?php

namespace Rmk\JsonApi\Contracts;

use stdClass;

/**
 * Interface for builders of value objects
 */
interface ValueObjectBuilder
{

    /**
     * Build the value object with the default or/and set values
     *
     * @return mixed
     */
    public function build();

    /**
     * Creates new instance of the builder
     *
     * Prefer this method instead of using builders constructor
     *
     * @return static
     */
    public static function instance(): self;

    /**
     * Creates new builder with the values from plain (stdClass) object
     *
     * @param stdClass $object
     *
     * @return static
     */
    public static function fromPlainObject(stdClass $object): self;
}
