<?php

namespace Rmk\JsonApi\Document\ValueObject;

use JsonSerializable;
use Rmk\JsonApi\Contracts\DocumentData;
use Rmk\JsonApi\Document\Collection\LinksCollection;
use Rmk\JsonApi\Document\Collection\ResourcesCollection;
use stdClass;

/**
 * Relationships
 *
 * The value of the relationships key MUST be an object (a “relationships object”). Members of the relationships object
 * (“relationships”) represent references from the resource object in which it’s defined to other resource objects.
 * Relationships may be to-one or to-many.
 */
class Relationship implements JsonSerializable
{

    /**
     * @var LinksCollection
     */
    private LinksCollection $links;

    /**
     * @var DocumentData
     */
    private DocumentData $data;

    /**
     * @var stdClass|null
     */
    private ?stdClass $meta;

    /**
     * @param ResourcesCollection|null $data
     * @param LinksCollection|null $links
     * @param stdClass|null $meta
     */
    public function __construct(DocumentData $data = null, LinksCollection $links = null, ?stdClass $meta = null)
    {
        $this->links = $links ?? new LinksCollection();
        $this->data = $data ?? new ResourcesCollection();
        $this->meta = $meta;
    }

    /**
     * @return LinksCollection
     */
    public function getLinks(): LinksCollection
    {
        return $this->links;
    }

    /**
     * @return DocumentData
     */
    public function getData(): DocumentData
    {
        return $this->data;
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
        $json = [];
        if ($this->getLinks()) {
            $json['links'] = $this->getLinks();
        }
        if ($this->getData()) {
            $json['data'] = $this->getData();
        }
        if ($this->getMeta()) {
            $json['meta'] = $this->getMeta();
        }

        return $json;
    }
}
