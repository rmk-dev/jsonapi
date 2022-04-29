<?php

namespace Rmk\JsonApi\Document\Builder;

use Rmk\JsonApi\Document\ValueObject\Link;
use Rmk\JsonApi\Exception\InvalidPlainObjectException;
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
        return static::instance()
            ->withMeta($link->getMeta())
            ->withHref($link->getHref());
    }

    public static function fromPlainObject($object): self
    {
        if (is_string($object)) {
            $link = $object;
            $object = new stdClass();
            $object->href = $link;
        }
        if (!($object instanceof stdClass)) {
             throw new InvalidPlainObjectException('Link must be either a stdClass instance or string');
        }
        $builder = static::instance();
        if (!empty($object->meta)) {
            $builder->withMeta($object->meta);
        }
        if (!empty($object->href)) {
            $builder->withHref($object->href);
        }

        return $builder;
    }
}
