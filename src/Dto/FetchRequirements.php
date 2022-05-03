<?php

namespace Rmk\JsonApi\Dto;

/**
 * Some requirements for fetching data like fields, filters, sorting, etc...
 */
class FetchRequirements
{

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
     * @param string[] $fields
     * @param array $sorting
     * @param array|iterable $filters
     */
    public function __construct(array $fields, array $sorting, $filters)
    {
        $this->fields = $fields;
        $this->sorting = $sorting;
        $this->filters = $filters;
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
     * @return FetchRequirements
     */
    public function setFields(array $fields): FetchRequirements
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
     * @return FetchRequirements
     */
    public function setSorting(array $sorting): FetchRequirements
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
     * @return FetchRequirements
     */
    public function setFilters(iterable $filters): FetchRequirements
    {
        $this->filters = $filters;

        return $this;
    }
}
