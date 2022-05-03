<?php

namespace Rmk\JsonApi\Contracts;

use Rmk\JsonApi\Document\Collection\ResourcesCollection;
use Rmk\JsonApi\Document\ValueObject\Resource;
use Rmk\JsonApi\Dto\FetchRequirements;
use Rmk\JsonApi\Dto\PaginationRequirements;
use Rmk\JsonApi\Exception\RelationshipDoesNotExistsException;
use Rmk\JsonApi\Exception\ResourceNotFoundException;

/**
 * Interface for classes that loads resources from data storage (e.g. DB, files, curl, etc...)
 */
interface ResourceReader
{

    /**
     * Loads single resource by its identification
     *
     * If no resource is found with the such identification it must throw ResourceNotFound exception
     *
     * @param string           $id     The resource identification
     * @param iterable<string> $fields Fields to be loaded. Default empty array means loading of all fields
     *
     * @return Resource Object with the loaded data
     *
     * @throws ResourceNotFoundException
     */
    public function read(string $id, iterable $fields = []): Resource;

    /**
     * Loads collection of resources
     *
     * The collection may be paginated. By default, it should return all resources. If the collection is paginated
     * it should contain links about the pagination like "first", "last", "next", "prev".
     * Empty collection must be return if no data is loaded.
     *
     * @param FetchRequirements $fetchRequirements           Requirements for fetching data (fields, filters, sorting)
     * @param PaginationRequirements $paginationRequirements Requirements for paginating (items per page, current page)
     *
     * @return ResourcesCollection Collection with resource objects and pagination links
     */
    public function readCollection(
        FetchRequirements $fetchRequirements,
        PaginationRequirements $paginationRequirements
    ): ResourcesCollection;

    /**
     * Loads a relationship for an object
     *
     * The method loads either a single relationship object or relationship collection. If the main resource is not
     * found it must throw ResourceNotFoundException. If the resource does not have a relationship with such name it
     * must throw RelationshipDoesNotExistsException.
     *
     * @param string                 $id                     The main resource identification
     * @param string                 $name                   The name of the relationship
     * @param FetchRequirements      $fetchRequirements      Requirements for fetching data (fields, filters, sorting)
     * @param PaginationRequirements $paginationRequirements Requirements for paginating (items per page, current page)
     *
     * @return Relationship
     *
     * @throws ResourceNotFoundException;
     * @throws RelationshipDoesNotExistsException
     */
    public function readRelation(
        string $id,
        string $name,
        FetchRequirements $fetchRequirements,
        PaginationRequirements $paginationRequirements
    ): Relationship;
}
