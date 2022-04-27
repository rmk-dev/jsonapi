<?php

namespace Rmk\JsonApi\Document\Collection;

use Rmk\Collections\AbstractClassCollection;
use Rmk\JsonApi\Document\ValueObject\Link;

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
