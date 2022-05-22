<?php

namespace Rmk\JsonApi\Dto;

/**
 * Some requirements for fetching data like fields, filters, sorting, etc...
 */
class QueryParameters
{

    /**
     * @var string
     */
    protected string $id = '';

    /**
     * @var string
     */
    protected string $type = '';

    /**
     * @var string
     */
    protected string $relationName = '';

    /**
     * @var array<int, string>
     */
    protected array $fields = [];

    /**
     * @var array<string, mixed>
     */
    protected array $sorting = [];

    /**
     * @var iterable
     */
    protected iterable $filters = [];

    /**
     * @param string $id
     * @param string $type
     * @param string $relationName
     * @param string[] $fields
     * @param array $sorting
     * @param iterable $filters
     */
    public function __construct(
        string $id = '',
        string $type = '',
        string $relationName = '',
        array $fields = [],
        array $sorting = [],
        iterable $filters = []
    ) {
        $this->setId($id);
        $this->setType($type);
        $this->setRelationName($relationName);
        $this->setFields($fields);
        $this->setSorting($sorting);
        $this->setFilters($filters);
    }

    /**
     * @return string[]
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * @param string[] $fields
     *
     * @return QueryParameters
     */
    public function setFields(array $fields): QueryParameters
    {
        $this->fields = $fields;
        return $this;
    }

    /**
     * @return array
     */
    public function getSorting(): array
    {
        return $this->sorting;
    }

    /**
     * @param array $sorting
     *
     * @return QueryParameters
     */
    public function setSorting(array $sorting): QueryParameters
    {
        $this->sorting = $sorting;
        return $this;
    }

    /**
     * @return iterable
     */
    public function getFilters(): iterable
    {
        return $this->filters;
    }

    /**
     * @param iterable $filters
     *
     * @return QueryParameters
     */
    public function setFilters(iterable $filters): QueryParameters
    {
        $this->filters = $filters;

        return $this;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     * @return QueryParameters
     */
    public function setId(string $id): QueryParameters
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return QueryParameters
     */
    public function setType(string $type): QueryParameters
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return string
     */
    public function getRelationName(): string
    {
        return $this->relationName;
    }

    /**
     * @param string $relationName
     * @return QueryParameters
     */
    public function setRelationName(string $relationName): QueryParameters
    {
        $this->relationName = $relationName;
        return $this;
    }
}
