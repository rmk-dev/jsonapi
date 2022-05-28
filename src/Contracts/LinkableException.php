<?php

namespace Rmk\JsonApi\Contracts;

interface LinkableException
{

    public function setLink(string $url): void;

    public function getLink(): string;
}
