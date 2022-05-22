<?php

namespace Rmk\JsonApi\Action;

use Rmk\JsonApi\Action\Collection\RemoversCollection;
use Rmk\JsonApi\Exception\ResourceNotFoundException;
use Rmk\JsonApi\Exception\ResourceRemovingException;
use Rmk\JsonApi\Exception\UnsupportedResourceTypeException;

/**
 * Removing data service
 */
class RemoveData
{

    protected RemoversCollection $removers;

    /**
     * @param RemoversCollection $removers
     */
    public function __construct(RemoversCollection $removers)
    {
        $this->removers = $removers;
    }

    /**
     * Performs removing of a resource from given type with given id
     *
     * @param string $type
     * @param string $id
     *
     * @return bool
     *
     * @throws ResourceRemovingException
     * @throws ResourceNotFoundException
     */
    public function execute(string $type, string $id): bool
    {
        if (!$this->removers->has($type)) {
            throw new UnsupportedResourceTypeException(
                sprintf('Resource with type %s is not supported for removing', $type)
            );
        }
        try {
            return $this->removers->get($type)->remove($id);
        } catch (\Throwable $exception) {
            if ($exception instanceof ResourceNotFoundException) {
                throw $exception;
            }

            throw new ResourceRemovingException('Resource is not removed', 1, $exception);
        }
    }
}
