<?php

namespace Rmk\JsonApi\Action\Collection;

use Rmk\Collections\AbstractClassCollection;
use Rmk\JsonApi\Contracts\ResourceReader;

/**
 * Collection with data readers
 */
class ReadersCollection extends AbstractClassCollection
{

    /**
     * @return string
     */
    public function getClassName(): string
    {
        return ResourceReader::class;
    }
}
