<?php

namespace RmkTests\JsonApi;

use PHPUnit\Framework\TestCase;
use Rmk\JsonApi\Contracts\DocumentData;
use Rmk\JsonApi\Document\Builder\DocumentBuilder;
use Rmk\JsonApi\Document\Collection\ErrorsCollection;
use Rmk\JsonApi\Document\Collection\LinksCollection;
use Rmk\JsonApi\Document\Collection\ResourcesCollection;
use Rmk\JsonApi\Document\ValueObject\Document;
use Rmk\JsonApi\Document\ValueObject\Error;
use Rmk\JsonApi\Document\ValueObject\JsonApi;
use Rmk\JsonApi\Document\ValueObject\ResourceIdentifier;

class DocumentBuilderTest extends TestCase
{

    private LinksCollection $links;
    private ResourcesCollection $included;
    private \stdClass $meta;
    private JsonApi $jsonApi;
    private DocumentData $data;

    protected function setUp(): void
    {
        $this->links = new LinksCollection();
        $this->included = new ResourcesCollection();
        $this->meta = new \stdClass();
        $this->jsonApi = new JsonApi(JsonApi::VERSION_DEFAULT);
        $this->data = new ResourceIdentifier('1', 'test');
    }

    public function testDefaultBuildingOfDocument(): void
    {
        $document = DocumentBuilder::instance()
            ->withData($this->data)
            ->withLinks($this->links)
            ->withIncluded($this->included)
            ->withMeta($this->meta)
            ->withJsonApi($this->jsonApi)
            ->build();

        $this->assertSame($this->data, $document->getData());
        $this->assertSame($this->links, $document->getLinks());
        $this->assertSame($this->included, $document->getIncluded());
        $this->assertSame($this->meta, $document->getMeta());
        $this->assertSame($this->jsonApi, $document->getJsonApi());
    }

    public function testBuildDocumentFromPrototype(): void
    {
        $proto = new Document($this->data, $this->links, $this->jsonApi, $this->included, $this->meta);
        $document = DocumentBuilder::fromDocument($proto)->build();
        $this->assertSame($this->data, $document->getData());
        $this->assertSame($this->links, $document->getLinks());
        $this->assertSame($this->included, $document->getIncluded());
        $this->assertSame($this->meta, $document->getMeta());
        $this->assertSame($this->jsonApi, $document->getJsonApi());
    }

    public function testBuildDocumentFromPrototypeWithErrorsCollection(): void
    {
        $proto = new Document(new ErrorsCollection([new Error()]), $this->links, $this->jsonApi, $this->included, $this->meta);
        $document = DocumentBuilder::fromDocument($proto)->build();
        $this->assertNotSame($this->data, $document->getData());
        $this->assertInstanceOf(ResourceIdentifier::class, $document->getData());
        $this->assertEquals(ResourceIdentifier::EMPTY_TYPE, $document->getData()->getType());
        $this->assertEquals(ResourceIdentifier::EMPTY_ID, $document->getData()->getId());
        $this->assertSame($this->links, $document->getLinks());
        $this->assertSame($this->included, $document->getIncluded());
        $this->assertSame($this->meta, $document->getMeta());
        $this->assertSame($this->jsonApi, $document->getJsonApi());
    }
}
