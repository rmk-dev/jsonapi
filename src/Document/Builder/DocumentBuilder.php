<?php

namespace Rmk\JsonApi\Document\Builder;

use Rmk\JsonApi\Contracts\DocumentData;
use Rmk\JsonApi\Contracts\ValueObjectBuilder;
use Rmk\JsonApi\Document\Collection\ErrorsCollection;
use Rmk\JsonApi\Document\Collection\LinksCollection;
use Rmk\JsonApi\Document\Collection\ResourcesCollection;
use Rmk\JsonApi\Document\ValueObject\Document;
use Rmk\JsonApi\Document\ValueObject\JsonApi;
use Rmk\JsonApi\Document\ValueObject\Resource;
use Rmk\JsonApi\Exception\InvalidPlainObjectException;
use stdClass;

/**
 * Builds document (top-level) value object
 */
class DocumentBuilder implements ValueObjectBuilder
{

    /**
     * The document’s “primary data” or a collection of error objects
     *
     * @var DocumentData
     */
    protected DocumentData $data;

    /**
     * Document meta
     *
     * @var stdClass|null
     */
    protected ?stdClass $meta = null;

    /**
     * The JsonApi info
     *
     * @var JsonApi|null
     */
    protected ?JsonApi $jsonApi = null;

    /**
     * Document links
     *
     * @var LinksCollection
     */
    protected LinksCollection $links;

    /**
     * Included resources to the result
     *
     * @var ResourcesCollection
     */
    protected ResourcesCollection $included;

    protected function __construct()
    {
        $this->links = new LinksCollection();
        $this->included = new ResourcesCollection();
    }

    /**
     * Set document's data or errors collection
     *
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
     * Sets document's links
     *
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
     * Sets document's included resources
     *
     * @param ResourcesCollection $included
     *
     * @return $this
     */
    public function withIncluded(ResourcesCollection $included): self
    {
        $this->included = $included;

        return $this;
    }

    /**
     * Sets document's meta
     *
     * @param stdClass|null $meta
     *
     * @return $this
     */
    public function withMeta(?stdClass $meta): self
    {
        $this->meta = $meta;

        return $this;
    }

    /**
     * Sets Json API version information
     *
     * @param JsonApi $jsonApi
     *
     * @return $this
     */
    public function withJsonApi(JsonApi $jsonApi): self
    {
        $this->jsonApi = $jsonApi;

        return $this;
    }

    public function build(): Document
    {
        return new Document($this->data, $this->links, $this->jsonApi, $this->included, $this->meta);
    }

    /**
     * Create new builder instance
     *
     * @return static
     */
    public static function instance(): self
    {
        return new self();
    }

    /**
     * Creates new builder with data, copied by a prototype document
     *
     * @param Document $prototype
     *
     * @return static
     */
    public static function fromDocument(Document $prototype): self
    {
        $builder = static::instance()
            ->withLinks($prototype->getLinks())
            ->withIncluded($prototype->getIncluded())
            ->withMeta($prototype->getMeta())
            ->withJsonApi($prototype->getJsonApi());

        if ($prototype->getErrors()->count()) {
            $builder->withData($prototype->getErrors());
        } else {
            $builder->withData($prototype->getData());
        }
        return $builder;
    }

    public static function fromPlainObject(stdClass $object): self
    {
        if (empty($object->data) && empty($object->errors) && empty($object->meta)) {
            throw new InvalidPlainObjectException(
                'A document MUST contain at least one of the "data", "errors" or "meta" top-level members'
            );
        }
        $builder = static::instance();
        if (!empty($object->jsonapi) && isset($object->jsonapi->version)) {
            $builder->withJsonApi(new JsonApi($object->jsonapi->version));
        }
        if (!empty($object->meta)) {
            $builder->withMeta($object->meta);
        }

        if (!empty($object->data)) {
            if (is_array($object->data)) {
                $data = new ResourcesCollection();
                foreach ($object->data as $res) {
                    $data->append(ResourceBuilder::fromPlainObject($res)->build());
                }
            } else {
                $data = ResourceBuilder::fromPlainObject($object->data)->build();
            }
            $builder->withData($data);
        } else if (!empty($object->errors) && is_array($object->errors)) {
            $errors = new ErrorsCollection();
            foreach ($object->errors as $error) {
                $errors->append(ErrorBuilder::fromPlainObject($error));
            }
            $builder->withData($errors);
        }
        return $builder;
    }
}
