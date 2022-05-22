<?php

namespace Rmk\JsonApi\Contracts;

use Rmk\JsonApi\Exception\ResourceNotFoundException;

/**
 * Interface for object that are used for deleting resources
 */
interface ResourceRemover
{

    /**
     * Deletes a resource with specific identification
     *
     * If a resource with such id is not found, the method must return ResourceNotFoundException.
     *
     * @param string $id
     *
     * @return bool True if the resources is deleted successfully, otherwise false
     *
     * @throws ResourceNotFoundException
     */
    public function remove(string $id): bool;
}
