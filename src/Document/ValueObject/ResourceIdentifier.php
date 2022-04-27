<?php

namespace Rmk\JsonApi\Document\ValueObject;

use JsonSerializable;
use Rmk\JsonApi\Contracts\DataInterface;

/**
 * Resource identification presentation
 *
 * "Resource objects” appear in a JSON:API documents to represent resources.
 * Every resource object MUST contain an id member and a type member. The values of the id and type members
 * MUST be strings. Within a given API, each resource object’s type and id pair MUST identify a single, unique resource.
 * (The set of URIs controlled by a server, or multiple servers acting as one, constitute an API.)
 * The type member is used to describe resource objects that share common attributes and relationships.
 */
class ResourceIdentifier implements JsonSerializable, DataInterface
{

    /**
     * Resource identifier string
     *
     * @var string
     */
    protected string $id;

    /**
     * Resource type
     *
     * @var string
     */
    protected string $type;

    /**
     * @param string $id
     * @param string $type
     */
    public function __construct(string $id, string $type)
    {
        $this->id = $id;
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'id' => $this->getId(),
            'type' => $this->getType(),
        ];
    }
}
