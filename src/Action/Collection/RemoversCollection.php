<?php

namespace Rmk\JsonApi\Action\Collection;

use Rmk\Collections\AbstractClassCollection;
use Rmk\JsonApi\Contracts\ResourceRemover;

/**
 * Collection with data removers
 */
class RemoversCollection extends AbstractClassCollection
{

    /**
     * @return string
     */
    public function getClassName(): string
    {
        return ResourceRemover::class;
    }
}
