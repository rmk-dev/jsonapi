<?php

namespace Rmk\JsonApi\Document\ValueObject;

use Rmk\Collections\AbstractClassCollection;

class RelationshipsCollection extends AbstractClassCollection
{
    public function getClassName(): string
    {
        return Relationship::class;
    }
}
