<?php

namespace Rmk\JsonApi\Document\ValueObject;

use Rmk\Collections\AbstractClassCollection;

/**
 * Collection with links
 */
class LinksCollection extends AbstractClassCollection
{

    /**
     * The class name of the collection elements
     *
     * @return string
     */
    public function getClassName(): string
    {
        return Link::class;
    }
}
