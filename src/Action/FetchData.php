<?php

namespace Rmk\JsonApi\Action;

use Rmk\Collections\Collection;
use Rmk\JsonApi\Contracts\Relationship;
use Rmk\JsonApi\Contracts\ResourceReader;
use Rmk\JsonApi\Document\Collection\ResourcesCollection;
use Rmk\JsonApi\Document\ValueObject\Resource;
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
     * @param string $type
     * @param string $id
     * @param string $relation
     *
     * @return Relationship|ResourcesCollection|Resource
     *
     * @throws RelationshipDoesNotExistsException
     * @throws ResourceNotFoundException
     */
    public function execute(string $type, string $id = '', string $relation = '')
    {
        /** @var ResourceReader $reader */
        $reader = $this->readers->get($type);
        if ($id) {
            if ($relation) {
                return $reader->readRelation($id, $relation);
            } else {
                return $reader->read($id);
            }
        } else {
            return $reader->readCollection($type);
        }
    }
}
