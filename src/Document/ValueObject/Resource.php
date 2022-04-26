<?php

namespace Rmk\JsonApi\Document\ValueObject;

use stdClass;

/**
 * "Resource objects” appear in a JSON:API documents to represent resources.
 */
class Resource extends ResourceIdentifier
{

    /**
     * An attributes object representing some of the resource’s data.
     *
     * @var stdClass|null
     */
    private ?stdClass $attributes;

    /**
     * A relationships object describing relationships between the resource and other JSON:API resources
     *
     * @var RelationshipsCollection
     */
    private RelationshipsCollection $relationships;

    /**
     * A links object containing links related to the resource.
     *
     * @var LinksCollection
     */
    private LinksCollection $links;

    /**
     * A meta object containing non-standard meta-information about a resource that can not be represented
     * as an attribute or relationship.
     *
     * @var stdClass|null
     */
    private ?stdClass $meta;

    /**
     * @param stdClass|null $attributes
     * @param RelationshipsCollection|null $relationships
     * @param LinksCollection|null $links
     * @param stdClass|null $meta
     */
    public function __construct(
        ?stdClass $attributes = null,
        RelationshipsCollection $relationships = null,
        LinksCollection $links = null,
        ?stdClass $meta = null
    ) {
        $this->attributes = $attributes;
        if ($relationships) {
            $this->relationships = $relationships;
        } else {
            $this->relationships = new RelationshipsCollection();
        }
        if ($links) {
            $this->links = $links;
        } else {
            $this->links = new LinksCollection();
        }
        $this->meta = $meta;
    }

    /**
     * @return stdClass|null
     */
    public function getAttributes(): ?stdClass
    {
        return $this->attributes;
    }

    /**
     * @return RelationshipsCollection
     */
    public function getRelationships(): RelationshipsCollection
    {
        return $this->relationships;
    }

    /**
     * @return LinksCollection
     */
    public function getLinks(): LinksCollection
    {
        return $this->links;
    }

    /**
     * @return stdClass|null
     */
    public function getMeta(): ?stdClass
    {
        return $this->meta;
    }

    public function jsonSerialize()
    {
        $json = parent::jsonSerialize();
        if ($this->getAttributes()) {
            $json['attributes'] = $this->getAttributes();
        }
        if ($this->getLinks()->count()) {
            $json['links'] = $this->getLinks();
        }
        if ($this->getRelationships()->count()) {
            $json['relationships'] = $this->getRelationships();
        }
        if ($this->getMeta()) {
            $json['meta'] = $this->getMeta();
        }

        return $json;
    }
}
