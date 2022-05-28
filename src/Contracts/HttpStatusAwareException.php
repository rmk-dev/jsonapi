<?php

namespace Rmk\JsonApi\Contracts;

interface HttpStatusAwareException
{

    /**
     * @return int
     */
    public function getHttpStatus(): int;

    /**
     * @param int $httpStatus
     *
     * @return void
     */
    public function setHttpStatus(int $httpStatus): void;
}
