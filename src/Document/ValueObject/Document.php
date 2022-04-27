<?php

namespace Rmk\JsonApi\Document\ValueObject;

use JsonSerializable;
use Rmk\JsonApi\Contracts\DataInterface;
use Rmk\JsonApi\Document\Collection\ErrorsCollection;
use Rmk\JsonApi\Document\Collection\LinksCollection;
use Rmk\JsonApi\Document\Collection\ResourcesCollection;
use stdClass;

/**
 * Top Level
 *
 * A JSON object MUST be at the root of every JSON:API request and response containing data.
 * This object defines a document’s “top level”.
 */
class Document implements JsonSerializable
{

    /**
     * An object describing the server’s implementation
     *
     * @var JsonApi
     */
    private JsonApi $jsonApi;

    /**
     * The document’s “primary data”
     *
     * @var DataInterface
     */
    private DataInterface $data;

    /**
     * Error objects
     *
     * @var ErrorsCollection
     */
    private ErrorsCollection $errors;

    /**
     * A links object related to the primary data.
     *
     * @var LinksCollection
     */
    private LinksCollection $links;

    /**
     * Resource objects that are related to the primary data and/or each other (“included resources”).
     *
     * @var ResourcesCollection
     */
    private ResourcesCollection $included;

    /**
     * A meta-object that contains non-standard meta-information.
     *
     * @var stdClass|null
     */
    private ?stdClass $meta;

    /**
     * @param DataInterface $data
     * @param LinksCollection|null $links
     * @param JsonApi|null $jsonApi
     * @param ResourcesCollection|null $included
     * @param stdClass|null $meta
     */
    public function __construct(
        DataInterface $data,
        LinksCollection $links = null,
        JsonApi $jsonApi = null,
        ResourcesCollection $included = null,
        ?stdClass $meta = null
    ) {
        if ($data instanceof ErrorsCollection) {
            $this->errors = $data;
        } else {
            $this->data = $data;
        }
        if ($jsonApi) {
            $this->jsonApi = $jsonApi;
        } else {
            $this->jsonApi = new JsonApi(JsonApi::VERSION_DEFAULT);
        }
        if ($links) {
            $this->links = $links;
        } else {
            $this->links = new LinksCollection();
        }
        if ($included) {
            $this->included = $included;
        } else {
            $this->included = new ResourcesCollection();
        }
        $this->meta = $meta;
    }

    /**
     * @return JsonApi
     */
    public function getJsonApi(): JsonApi
    {
        return $this->jsonApi;
    }

    /**
     * @return DataInterface
     */
    public function getData(): DataInterface
    {
        return $this->data;
    }

    /**
     * @return ErrorsCollection
     */
    public function getErrors(): ErrorsCollection
    {
        return $this->errors;
    }

    /**
     * @return LinksCollection
     */
    public function getLinks(): LinksCollection
    {
        return $this->links;
    }

    /**
     * @return ResourcesCollection
     */
    public function getIncluded(): ResourcesCollection
    {
        return $this->included;
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
        $json = [
            'jsonApi' => $this->getJsonApi(),
        ];
        if ($this->getErrors()->count()) {
            $json['errors'] = $this->getErrors();
        } else if ($this->getData()->count()) {
            $json['data'] = $this->getData();
            if ($this->getIncluded()->count()) {
                $json['included'] = $this->getIncluded();
            }
        }
        if ($this->getLinks()->count()) {
            $json['links'] = $this->getLinks();
        }
        if ($this->getMeta()) {
            $json['meta'] = $this->getMeta();
        }

        return $json;
    }
}
