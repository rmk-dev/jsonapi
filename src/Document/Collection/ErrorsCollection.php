<?php

namespace Rmk\JsonApi\Document\Collection;

use Rmk\Collections\AbstractClassCollection;
use Rmk\JsonApi\Contracts\DataInterface;
use Rmk\JsonApi\Document\ValueObject\Error;

class ErrorsCollection extends AbstractClassCollection implements DataInterface
{
    public function getClassName(): string
    {
        return Error::class;
    }
}
