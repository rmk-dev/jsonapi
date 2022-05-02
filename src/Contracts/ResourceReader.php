<?php

namespace Rmk\JsonApi\Contracts;

use Rmk\JsonApi\Document\Collection\ResourcesCollection;
use Rmk\JsonApi\Document\ValueObject\Resource;
use Rmk\JsonApi\Exception\RelationshipDoesNotExistsException;
use Rmk\JsonApi\Exception\ResourceNotFoundException;

/**
 * Interface for classes that loads resources from data storage (e.g. DB, files, curl, etc...)
 */
interface ResourceReader
{

    /**
     * Default items per page for the collections. Zero means all items.
     */
    public const DEFAULT_PER_PAGE = 0;

    /**
     * Default page of the collection. Zero means no pagination.
     */
    public const DEFAULT_PAGE = 0;

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
     * @param int      $perPage The number of resource per page. Default 0 means loading of all resources
     * @param int      $page    The number of the current page. Default 0 means loading of all resources
     * @param iterable $filters Filters for loading collection. Default none.
     * @param iterable $fields  Fields to be loaded. Default empty array means loading of all fields
     *
     * @return ResourcesCollection Collection with resource objects and pagination links
     */
    public function readCollection(
        int $perPage = self::DEFAULT_PER_PAGE,
        int $page = self::DEFAULT_PAGE,
        iterable $filters = [],
        iterable $fields = []
    ): ResourcesCollection;

    /**
     * Loads a relationship for an object
     *
     * The method loads either a single relationship object or relationship collection. If the main resource is not
     * found it must throw ResourceNotFoundException. If the resource does not have a relationship with such name it
     * must throw RelationshipDoesNotExistsException.
     *
     * @param string $id   The main resource identification
     * @param string $name The name of the relationship
     * @param int $perPage The number of resource per page if the relationship is collection. Default all items.
     * @param int $page    The current page if the relationship is collection. Default 0 means no pagination.
     *
     * @return Relationship
     *
     * @throws ResourceNotFoundException;
     * @throws RelationshipDoesNotExistsException
     */
    public function readRelation(
        string $id,
        string $name,
        int $perPage = self::DEFAULT_PER_PAGE,
        int $page = self::DEFAULT_PAGE
    ): Relationship;
}
