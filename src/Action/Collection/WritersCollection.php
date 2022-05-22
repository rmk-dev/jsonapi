<?php

namespace Rmk\JsonApi\Action\Collection;

use Rmk\Collections\AbstractClassCollection;
use Rmk\JsonApi\Contracts\ResourceWriter;

class WritersCollection extends AbstractClassCollection
{

    /**
     * @return string
     */
    public function getClassName(): string
    {
        return ResourceWriter::class;
    }
}
