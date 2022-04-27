<?php

namespace Rmk\JsonApi\Document\Builder;

// TODO buildResource(), buildIdentifier()
use Rmk\JsonApi\Document\Collection\LinksCollection;
use Rmk\JsonApi\Document\Collection\RelationshipsCollection;
use Rmk\JsonApi\Document\ValueObject\Link;
use Rmk\JsonApi\Document\ValueObject\Relationship;
use Rmk\JsonApi\Document\ValueObject\Resource;
use Rmk\JsonApi\Document\ValueObject\ResourceIdentifier;
use stdClass;

/**
 * Build resources
 */
class ResourceBuilder
{

    /**
     * @var string
     */
    private string $id;

    /**
     * @var string
     */
    private string $type;

    /**
     * @var stdClass|null
     */
    private ?stdClass $attributes = null;

    /**
     * @var RelationshipsCollection|null
     */
    private ?RelationshipsCollection $relations;

    /**
     * @var LinksCollection|null
     */
    private ?LinksCollection $links;

    /**
     * @var stdClass|null
     */
    private ?stdClass $meta = null;

    private function __construct()
    {
        $this->relations = new RelationshipsCollection();
        $this->links = new LinksCollection();
    }

    /**
     * @param string $id
     *
     * @return $this
     */
    public function withId(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @param string $type
     *
     * @return $this
     */
    public function withType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return ResourceIdentifier
     */
    public function buildIdentifier(): ResourceIdentifier
    {
        return new ResourceIdentifier($this->id, $this->type);
    }

    /**
     * @param stdClass $attributes
     *
     * @return $this
     */
    public function withAttributes(stdClass $attributes): self
    {
        $this->attributes = $attributes;

        return $this;
    }

    /**
     * @param LinksCollection $links
     *
     * @return $this
     */
    public function withLinks(LinksCollection $links): self
    {
        $this->links = $links;

        return $this;
    }

    /**
     * @param RelationshipsCollection $relations
     *
     * @return $this
     */
    public function withRelations(RelationshipsCollection $relations): self
    {
        $this->relations = $relations;

        return $this;
    }

    /**
     * @param Link $link
     * @param string $name
     *
     * @return $this
     */
    public function withLink(Link $link, string $name): self
    {
        $this->links->set($name, $link);

        return $this;
    }

    /**
     * @param Relationship $relationship
     * @param string $name
     *
     * @return $this
     */
    public function withRelation(Relationship $relationship, string $name): self
    {
        $this->relations->set($name, $relationship);

        return $this;
    }

    /**
     * @param stdClass $meta
     *
     * @return $this
     */
    public function withMeta(stdClass $meta): self
    {
        $this->meta = $meta;

        return $this;
    }

    /**
     * @return Resource
     */
    public function buildResource(): Resource
    {
        return new Resource($this->id, $this->type, $this->attributes, $this->relations, $this->links, $this->meta);
    }

    /**
     * @return static
     */
    public static function instance(): self
    {
        return new self();
    }

    /**
     * @param ResourceIdentifier $resource
     *
     * @return static
     */
    public static function fromResourceIdentifier(ResourceIdentifier $resource): self
    {
        $builder = self::instance();
        $builder->id = $resource->getId();
        $builder->type = $resource->getType();

        return $builder;
    }

    /**
     * @param Resource $resource
     *
     * @return static
     */
    public static function fromResource(Resource $resource): self
    {
        $builder = self::fromResourceIdentifier($resource);
        $builder->attributes = $resource->getAttributes();
        $builder->relations = $resource->getRelationships();
        $builder->links = $resource->getLinks();
        $builder->meta = $resource->getMeta();

        return $builder;
    }
}
