<?php

namespace Rmk\JsonApi\Document\ValueObject;

use JsonSerializable;
use stdClass;

/**
 * JSON API value object
 */
class JsonApi implements JsonSerializable
{

    /**
     * JSON API version string
     *
     * @var string
     */
    protected string $version;

    /**
     * Object meta info
     *
     * @var stdClass|null
     */
    protected ?stdClass $meta;

    /**
     * @param string $version
     * @param stdClass|null $meta
     */
    public function __construct(string $version, ?stdClass $meta = null)
    {
        $this->version = $version;
        $this->meta = $meta;
    }

    /**
     * Get JSON API version string
     *
     * @return string
     */
    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * Object's meta information
     *
     * @return stdClass|null
     */
    public function getMeta(): ?stdClass
    {
        return $this->meta;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return array_filter([
            'version' => $this->getVersion(),
            'meta' => $this->getMeta(),
        ]);
    }
}
