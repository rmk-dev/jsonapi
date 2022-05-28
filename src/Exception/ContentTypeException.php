<?php

namespace Rmk\JsonApi\Exception;

use Exception;
use Rmk\JsonApi\Contracts\HttpStatusAwareException;
use Rmk\JsonApi\Contracts\LinkableException;

class ContentTypeException extends Exception implements HttpStatusAwareException, LinkableException
{

    public const INVALID_CONTENT_TYPE_HEADER = 1;

    public const INVALID_ACCEPT_HEADER = 2;

    protected int $httpStatus = 400;

    protected string $link = '';

    /**
     * @return int
     */
    public function getHttpStatus(): int
    {
        return $this->httpStatus;
    }

    /**
     * @param int $httpStatus
     * @return void
     */
    public function setHttpStatus(int $httpStatus): void
    {
        $this->httpStatus = $httpStatus;
    }

    /**
     * @return string
     */
    public function getLink(): string
    {
        return $this->link;
    }

    /**
     * @param string $url
     *
     * @return void
     */
    public function setLink(string $url): void
    {
        $this->link = $url;
    }
}
