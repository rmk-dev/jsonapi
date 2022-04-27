<?php

namespace Rmk\JsonApi\Document\Collection;

use Rmk\Collections\AbstractClassCollection;
use Rmk\JsonApi\Document\ValueObject\Relationship;

class RelationshipsCollection extends AbstractClassCollection
{
    public function getClassName(): string
    {
        return Relationship::class;
    }
}
