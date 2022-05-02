<?php

namespace Rmk\JsonApi\Contracts;

interface Relationship
{
    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName(string $name): self;
}
