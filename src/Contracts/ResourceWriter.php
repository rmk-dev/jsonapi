<?php

namespace Rmk\JsonApi\Contracts;

use Rmk\JsonApi\Document\ValueObject\Resource;
use Throwable;

/**
 * Interface for classes that are responsible for writing data to outer data source (DB, etc...)
 */
interface ResourceWriter
{

    /**
     * Writes a resource to data source
     *
     * This method must be used for any kind of writing (create or update) and must return the resource after
     * the operation is finished, updated with all additional data (if any). If the operation fail, the method must
     * throw the exception that caused the failure.
     *
     * @param Resource $resource
     *
     * @return Resource
     *
     * @throws Throwable
     */
    public function write(Resource $resource): Resource;
}
