<?php

namespace RmkTests\JsonApi;

use PHPUnit\Framework\TestCase;
use Rmk\JsonApi\Document\Builder\DocumentBuilder;
use Rmk\JsonApi\Document\Collection\LinksCollection;
use Rmk\JsonApi\Document\Collection\ResourcesCollection;
use Rmk\JsonApi\Document\ValueObject\JsonApi;
use Rmk\JsonApi\Document\ValueObject\ResourceIdentifier;

class DocumentBuilderTest extends TestCase
{

    public function testDefaultBuildingOfDocument(): void
    {
        $links = new LinksCollection();
        $included = new ResourcesCollection();
        $meta = new \stdClass();
        $jsonApi = new JsonApi(JsonApi::VERSION_DEFAULT);
        $data = new ResourceIdentifier('1', 'test');
        $document = DocumentBuilder::instance()
            ->withData($data)
            ->withLinks($links)
            ->withIncluded($included)
            ->withMeta($meta)
            ->withJsonApi($jsonApi)
            ->build();

        $this->assertSame($data, $document->getData());
        $this->assertSame($links, $document->getLinks());
        $this->assertSame($included, $document->getIncluded());
        $this->assertSame($meta, $document->getMeta());
        $this->assertSame($jsonApi, $document->getJsonApi());
    }
}
