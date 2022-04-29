<?php

namespace Rmk\JsonApi\Document\Collection;

use Rmk\Collections\AbstractClassCollection;
use Rmk\JsonApi\Contracts\DocumentData;
use Rmk\JsonApi\Document\ValueObject\ResourceIdentifier;

class ResourcesCollection extends AbstractClassCollection implements DocumentData
{
    public function getClassName(): string
    {
        return ResourceIdentifier::class;
    }
}
