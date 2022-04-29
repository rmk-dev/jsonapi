<?php

namespace Rmk\JsonApi\Document\ValueObject;

use JsonSerializable;
use stdClass;

/**
 * Error presentation
 */
class Error implements JsonSerializable
{

    /**
     * A unique identifier for this particular occurrence of the problem.
     *
     * @var string
     */
    private string $id;

    /**
     * The HTTP status code applicable to this problem, expressed as a string value.
     *
     * @var string
     */
    private string $status;

    /**
     * An application-specific error code, expressed as a string value.
     *
     * @var string
     */
    private string $code;

    /**
     * A short, human-readable summary of the problem that SHOULD NOT change from occurrence to occurrence of
     * the problem, except for purposes of localization.
     *
     * @var string
     */
    private string $title;

    /**
     * A human-readable explanation specific to this occurrence of the problem.
     * Like title, this field’s value can be localized.
     *
     * @var string
     */
    private string $detail;

    /**
     * A links object containing "about" member -
     * a link that leads to further details about this particular occurrence of the problem.
     *
     * @var Link|null
     */
    private ?Link $link;

    /**
     * An object containing references to the source of the error
     *
     * @var null|ErrorSource
     */
    private ?ErrorSource $source;

    /**
     * A meta object containing non-standard meta-information about the error.
     *
     * @var null|stdClass
     */
    private ?stdClass $meta;

    /**
     * @param string $id
     * @param string $status
     * @param string $code
     * @param string $title
     * @param string $detail
     * @param Link|null $link
     * @param ErrorSource|null $source
     * @param stdClass|null $meta
     */
    public function __construct(
        string $id = '',
        string $status = '',
        string $code = '',
        string $title = '',
        string $detail = '',
        ?Link $link = null,
        ?ErrorSource $source = null,
        ?stdClass $meta = null
    ) {
        $this->id = $id;
        $this->status = $status;
        $this->code = $code;
        $this->title = $title;
        $this->detail = $detail;
        $this->link = $link;
        $this->source = $source;
        $this->meta = $meta;
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
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getDetail(): string
    {
        return $this->detail;
    }

    /**
     * @return Link|null
     */
    public function getLink(): ?Link
    {
        return $this->link;
    }

    /**
     * @return ErrorSource|null
     */
    public function getSource(): ?ErrorSource
    {
        return $this->source;
    }

    /**
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
            'id' => $this->getId(),
            'code' => $this->getCode(),
            'status' => $this->getStatus(),
            'title' => $this->getTitle(),
            'detail' => $this->getDetail(),
            'link' => $this->getLink(),
            'source' => $this->getSource(),
            'meta' => $this->getMeta(),
        ], function($element) {
            return !is_object($element) ? !empty($element) : !empty(json_decode(json_encode($element)));
        });
    }
}
