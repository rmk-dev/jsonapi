<?php

namespace Rmk\JsonApi\Document\Collection;

use Rmk\Collections\AbstractClassCollection;
use Rmk\JsonApi\Contracts\Relationship as RelationshipContract;
use Rmk\JsonApi\Document\ValueObject\Relationship;

class RelationshipsCollection extends AbstractClassCollection implements RelationshipContract
{

    /**
     * The relationship name
     *
     * @var string
     */
    protected string $name;

    /**
     * @return string
     */
    public function getClassName(): string
    {
        return Relationship::class;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }
}
