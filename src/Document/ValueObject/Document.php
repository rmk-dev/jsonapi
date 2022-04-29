<?php

namespace Rmk\JsonApi\Document\ValueObject;

use JsonSerializable;
use Rmk\JsonApi\Contracts\DocumentData;
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
    protected JsonApi $jsonApi;

    /**
     * The document’s “primary data”
     *
     * @var DocumentData
     */
    protected DocumentData $data;

    /**
     * Error objects
     *
     * @var ErrorsCollection
     */
    protected ErrorsCollection $errors;

    /**
     * A links object related to the primary data.
     *
     * @var LinksCollection
     */
    protected LinksCollection $links;

    /**
     * Resource objects that are related to the primary data and/or each other (“included resources”).
     *
     * @var ResourcesCollection
     */
    protected ResourcesCollection $included;

    /**
     * A meta-object that contains non-standard meta-information.
     *
     * @var stdClass|null
     */
    protected ?stdClass $meta;

    /**
     * @param DocumentData $data
     * @param LinksCollection|null $links
     * @param JsonApi|null $jsonApi
     * @param ResourcesCollection|null $included
     * @param stdClass|null $meta
     */
    public function __construct(
        DocumentData        $data,
        LinksCollection     $links = null,
        JsonApi             $jsonApi = null,
        ResourcesCollection $included = null,
        ?stdClass           $meta = null
    ) {
        $this->initData($data);
        $this->initJsonApi($jsonApi);
        $this->initLinks($links);
        $this->initIncludes($included);
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
     * @return DocumentData
     */
    public function getData(): DocumentData
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

    /**
     * @param DocumentData $data
     *
     * @return void
     */
    protected function initData(DocumentData $data): void
    {
        if ($data instanceof ErrorsCollection) {
            $this->errors = $data;
        } else {
            $this->data = $data;
        }
    }

    /**
     * @param LinksCollection|null $links
     *
     * @return void
     */
    protected function initLinks(?LinksCollection $links): void
    {
        $this->links = $links ?? new LinksCollection();
    }

    /**
     * @param ResourcesCollection|null $included
     *
     * @return void
     */
    protected function initIncludes(?ResourcesCollection $included): void
    {
        $this->included = $included ?? new ResourcesCollection();
    }

    /**
     * @param JsonApi|null $jsonApi
     *
     * @return void
     */
    protected function initJsonApi(?JsonApi $jsonApi): void
    {
        $this->jsonApi = $jsonApi ?? new JsonApi(JsonApi::VERSION_DEFAULT);
    }
}
