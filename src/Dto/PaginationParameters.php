<?php

namespace Rmk\JsonApi\Dto;

/**
 * Requirements for paginating collections
 */
class PaginationParameters
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
     * Items per page to display. If zero, then display all items
     *
     * @var int
     */
    protected int $perPage = 0;

    /**
     * Number of current page. Set zero if all items are displayed
     *
     * @var int
     */
    protected int $currentPage = 0;

    /**
     * @param int $perPage
     * @param int $currentPage
     */
    public function __construct(int $perPage = self::DEFAULT_PER_PAGE, int $currentPage = self::DEFAULT_PAGE)
    {
        $this->perPage = $perPage;
        $this->currentPage = $currentPage;
    }

    /**
     * @return int
     */
    public function getPerPage(): int
    {
        return $this->perPage;
    }

    /**
     * @param int $perPage
     *
     * @return PaginationParameters
     */
    public function setPerPage(int $perPage): PaginationParameters
    {
        $this->perPage = $perPage;

        return $this;
    }

    /**
     * @return int
     */
    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }

    /**
     * @param int $currentPage
     *
     * @return PaginationParameters
     */
    public function setCurrentPage(int $currentPage): PaginationParameters
    {
        $this->currentPage = $currentPage;

        return $this;
    }
}