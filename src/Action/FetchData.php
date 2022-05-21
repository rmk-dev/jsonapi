<?php

namespace Rmk\JsonApi\Action;

use Rmk\Collections\Collection;
use Rmk\JsonApi\Contracts\Relationship;
use Rmk\JsonApi\Contracts\ResourceReader;
use Rmk\JsonApi\Document\Collection\ResourcesCollection;
use Rmk\JsonApi\Document\ValueObject\Resource;
use Rmk\JsonApi\Dto\QueryParameters;
use Rmk\JsonApi\Dto\PaginationParameters;
use Rmk\JsonApi\Exception\RelationshipDoesNotExistsException;
use Rmk\JsonApi\Exception\ResourceNotFoundException;

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
     * @var Collection<string, ResourceReader>
     */
    protected Collection $readers;

    /**
     * @param Collection $readers
     */
    public function __construct(Collection $readers)
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
     */
    public function execute(QueryParameters $fetchRequirements, PaginationParameters $paginationRequirements)
    {
        $id = $fetchRequirements->getId();
        $type = $fetchRequirements->getType();
        /** @var ResourceReader $reader */
        $reader = $this->readers->get($type);
        if ($id) {
            $data = $this->fetchSingle($reader, $fetchRequirements, $paginationRequirements);
        } else {
            $data = $reader->readCollection($fetchRequirements, $paginationRequirements);
        }

        return $data;
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
