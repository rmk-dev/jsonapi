<?php

namespace Rmk\JsonApi\Action;

use Rmk\JsonApi\Action\Collection\ReadersCollection;
use Rmk\JsonApi\Contracts\Relationship;
use Rmk\JsonApi\Contracts\ResourceReader;
use Rmk\JsonApi\Document\Collection\ResourcesCollection;
use Rmk\JsonApi\Document\ValueObject\Resource;
use Rmk\JsonApi\Dto\QueryParameters;
use Rmk\JsonApi\Dto\PaginationParameters;
use Rmk\JsonApi\Exception\RelationshipDoesNotExistsException;
use Rmk\JsonApi\Exception\ResourceNotFoundException;
use Rmk\JsonApi\Exception\ResourceReadingException;
use Rmk\JsonApi\Exception\UnsupportedResourceTypeException;
use Throwable;

/**
 * Fetching data service
 *
 * Used for reading data and exporting it to Document-Data type
 */
class FetchData
{

    /**
     * Collection with available readers
     *
     * @var ReadersCollection<string, ResourceReader>
     */
    protected ReadersCollection $readers;

    /**
     * @param ReadersCollection $readers
     */
    public function __construct(ReadersCollection $readers)
    {
        $this->readers = $readers;
    }

    /**
     *
     * @param QueryParameters      $fetchRequirements
     * @param PaginationParameters $paginationRequirements
     *
     * @return Relationship|Resource|ResourcesCollection
     *
     * @throws RelationshipDoesNotExistsException
     * @throws ResourceNotFoundException
     * @throws ResourceReadingException
     */
    public function execute(QueryParameters $fetchRequirements, PaginationParameters $paginationRequirements)
    {
        $type = $fetchRequirements->getType();
        if (!$this->readers->has($type)) {
            throw new UnsupportedResourceTypeException('No reader is found for resource type: ' . $type);
        }
        /** @var ResourceReader $reader */
        $reader = $this->readers->get($type);
        $id = $fetchRequirements->getId();
        try {
            if ($id) {
                $data = $this->fetchSingle($reader, $fetchRequirements, $paginationRequirements);
            } else {
                $data = $reader->readCollection($fetchRequirements, $paginationRequirements);
            }

            return $data;
        } catch (Throwable $exception) {
            if ($exception instanceof RelationshipDoesNotExistsException || $exception instanceof ResourceNotFoundException) {
                throw $exception;
            }

            throw new ResourceReadingException('Resource cannot be read', 1, $exception);
        }
    }

    /**
     * @param ResourceReader $reader
     * @param QueryParameters $fetchRequirements
     * @param PaginationParameters $paginationRequirements
     *
     * @return Relationship|Resource
     *
     * @throws RelationshipDoesNotExistsException
     * @throws ResourceNotFoundException
     */
    protected function fetchSingle(
        ResourceReader $reader,
        QueryParameters $fetchRequirements,
        PaginationParameters $paginationRequirements
    ) {
        $id = $fetchRequirements->getId();
        $relationName = $fetchRequirements->getRelationName();
        if ($relationName) {
            $data = $reader->readRelation($id, $relationName, $fetchRequirements, $paginationRequirements);
        } else {
            $data = $reader->read($id, $fetchRequirements->getFields());
        }

        return $data;
    }
}
