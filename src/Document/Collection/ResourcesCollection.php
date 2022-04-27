<?php

namespace Rmk\JsonApi\Document\Collection;

use Rmk\Collections\AbstractClassCollection;
use Rmk\JsonApi\Contracts\DataInterface;
use Rmk\JsonApi\Document\ValueObject\ResourceIdentifier;

class ResourcesCollection extends AbstractClassCollection implements DataInterface
{
    public function getClassName(): string
    {
        return ResourceIdentifier::class;
    }
}
