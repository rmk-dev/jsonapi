<?php

namespace Rmk\JsonApi\Document\Builder;

use Rmk\JsonApi\Contracts\ValueObjectBuilder;
use Rmk\JsonApi\Document\ValueObject\Error;
use Rmk\JsonApi\Document\ValueObject\ErrorSource;
use Rmk\JsonApi\Document\ValueObject\Link;
use stdClass;

/**
 * Builds error objects
 */
class ErrorBuilder implements ValueObjectBuilder
{

    /**
     * A unique identifier for this particular occurrence of the problem.
     *
     * @var string
     */
    protected string $id = '';

    /**
     * The HTTP status code applicable to this problem, expressed as a string value.
     *
     * @var string
     */
    protected string $status = '';

    /**
     * An application-specific error code, expressed as a string value.
     *
     * @var string
     */
    protected string $code = '';

    /**
     * A short, human-readable summary of the problem that SHOULD NOT change from occurrence to occurrence of
     * the problem, except for purposes of localization.
     *
     * @var string
     */
    protected string $title = '';

    /**
     * A human-readable explanation specific to this occurrence of the problem.
     * Like title, this field’s value can be localized.
     *
     * @var string
     */
    protected string $detail = '';

    /**
     * A links object containing "about" member -
     * a link that leads to further details about this particular occurrence of the problem.
     *
     * @var Link|null
     */
    protected ?Link $link = null;

    /**
     * An object containing references to the source of the error
     *
     * @var null|ErrorSource
     */
    protected ?ErrorSource $source = null;

    /**
     * A meta object containing non-standard meta-information about the error.
     *
     * @var null|stdClass
     */
    protected ?stdClass $meta = null;

    /**
     * @param string $id
     * @return ErrorBuilder
     */
    public function withId(string $id): ErrorBuilder
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @param string $status
     * @return ErrorBuilder
     */
    public function withStatus(string $status): ErrorBuilder
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @param string $code
     * @return ErrorBuilder
     */
    public function withCode(string $code): ErrorBuilder
    {
        $this->code = $code;
        return $this;
    }

    /**
     * @param string $title
     * @return ErrorBuilder
     */
    public function withTitle(string $title): ErrorBuilder
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @param string $detail
     * @return ErrorBuilder
     */
    public function withDetail(string $detail): ErrorBuilder
    {
        $this->detail = $detail;
        return $this;
    }

    /**
     * @param Link $link
     * @return ErrorBuilder
     */
    public function withLink(Link $link): ErrorBuilder
    {
        $this->link = $link;
        return $this;
    }

    /**
     * @param ErrorSource|null $source
     * @return ErrorBuilder
     */
    public function withSource(?ErrorSource $source): ErrorBuilder
    {
        $this->source = $source;
        return $this;
    }

    /**
     * @param stdClass|null $meta
     * @return ErrorBuilder
     */
    public function withMeta(?stdClass $meta): ErrorBuilder
    {
        $this->meta = $meta;
        return $this;
    }

    public static function instance(): self
    {
        return new self();
    }

    public static function fromPlainObject(stdClass $object): self
    {
        $builder = static::instance();
        $builder->withId($object->id ?? $builder->id)
            ->withStatus($object->status ?? $builder->status)
            ->withCode($object->code ?? $builder->code)
            ->withTitle($object->title ?? $builder->title)
            ->withDetail($object->detail ?? $builder->detail);
        if (isset($object->link)) {
            $builder->withLink(LinkBuilder::fromPlainObject($object->link)->build());
        }
        if (isset($object->source)) {
            $builder->withSource(new ErrorSource(
                $object->source->pointer ?? '',
                $object->source->parameter ?? ''
            ));
        }
        if (isset($object->meta)) {
            $builder->withMeta($object->meta);
        }

        return $builder;
    }

    public function build(): Error
    {
        return new Error(
            $this->id,
            $this->status,
            $this->code,
            $this->title,
            $this->detail,
            $this->link,
            $this->source,
            $this->meta
        );
    }
}
