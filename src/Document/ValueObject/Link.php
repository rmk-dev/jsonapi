<?php

namespace Rmk\JsonApi\Document\ValueObject;

use JsonSerializable;
use stdClass;

/**
 * Link presentation
 */
class Link implements JsonSerializable
{

    public const TYPE_SELF = 'self';

    public const TYPE_ABOUT = 'about';

    /**
     * A string containing the link’s URL.
     *
     * @var string
     */
    private string $href;

    /**
     * A meta-object containing non-standard meta-information about the link.
     *
     * @var stdClass|null
     */
    private ?stdClass $meta;

    /**
     * @param string $href
     * @param stdClass|null $meta
     */
    public function __construct(string $href, ?stdClass $meta = null)
    {
        $this->href = $href;
        $this->meta = $meta;
    }

    /**
     * @return string
     */
    public function getHref(): string
    {
        return $this->href;
    }

    /**
     * @return stdClass|null
     */
    public function getMeta(): ?stdClass
    {
        return $this->meta;
    }

    /**
     * @return string[]
     */
    public function jsonSerialize()
    {
        $json = [];
        if ($this->getHref()) {
            $json['href'] = $this->getHref();
        }
        if ($this->getMeta()) {
            $json['meta'] = $this->getMeta();
        }

        return $json;
    }

    /**
     * Present the link as a string
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getHref();
    }
}
