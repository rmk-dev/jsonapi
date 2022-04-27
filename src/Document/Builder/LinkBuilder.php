<?php

namespace Rmk\JsonApi\Document\Builder;

use Rmk\JsonApi\Document\ValueObject\Link;
use stdClass;

/**
 * Build a link object
 */
class LinkBuilder
{

    /**
     * @var string
     */
    private string $href;

    /**
     * @var stdClass|null
     */
    private ?stdClass $meta = null;

    public function build(): Link
    {
        return new Link($this->href, $this->meta);
    }

    public function withHref(string $href): self
    {
        $this->href = $href;

        return $this;
    }

    public function withMeta(stdClass $meta): self
    {
        $this->meta = $meta;

        return $this;
    }

    public static function instance(): self
    {
        return new self();
    }

    public static function fromLink(Link $link): self
    {
        $builder = new self();
        $builder->href = $link->getHref();
        $builder->meta = $link->getMeta();

        return $builder;
    }
}
