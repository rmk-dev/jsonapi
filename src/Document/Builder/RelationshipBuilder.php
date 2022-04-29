<?php

namespace Rmk\JsonApi\Document\Builder;

use Rmk\JsonApi\Contracts\DocumentData;
use Rmk\JsonApi\Document\Collection\LinksCollection;
use Rmk\JsonApi\Document\ValueObject\Relationship;
use stdClass;

class RelationshipBuilder
{

    /**
     * The relation data
     *
     * @var DocumentData|null
     */
    protected ?DocumentData $data = null;

    /**
     * The relation links
     *
     * @var LinksCollection|null
     */
    protected ?LinksCollection $links = null;

    /**
     * The relation meta info
     *
     * @var stdClass|null
     */
    protected ?stdClass $meta = null;

    /**
     * @param DocumentData $data
     *
     * @return $this
     */
    public function withData(DocumentData $data): self
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @param LinksCollection $links
     *
     * @return RelationshipBuilder
     */
    public function withLinks(LinksCollection $links): RelationshipBuilder
    {
        $this->links = $links;
        return $this;
    }

    /**
     * @param stdClass|null $meta
     *
     * @return RelationshipBuilder
     */
    public function withMeta(?stdClass $meta): RelationshipBuilder
    {
        $this->meta = $meta;
        return $this;
    }

    /**
     * @return Relationship
     */
    public function build(): Relationship
    {
        return new Relationship($this->data, $this->links, $this->meta);
    }

    /**
     * @return static
     */
    public static function instance(): self
    {
        return new static();
    }

    /**
     * @param stdClass $object
     *
     * @return static
     */
    public static function fromPlainObject(stdClass $object): self
    {
        $builder = static::instance();
        if (isset($object->data)) {
            $resourceBuilder = ResourceBuilder::fromPlainObject($object->data);
            $builder->withData($resourceBuilder->buildResource());
        }
        if (isset($object->meta)) {
            $builder->withMeta($object->meta);
        }
        if (!empty($object->links)) {
            $linksCollection = new LinksCollection();
            foreach ($object->links as $name => $link) {
                $linksCollection->set($name, LinkBuilder::fromPlainObject($link)->build());
            }
            $builder->withLinks($linksCollection);
        }

        return $builder;
    }
}