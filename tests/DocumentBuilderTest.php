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
use Rmk\JsonApi\Document\ValueObject\ErrorSource;
use Rmk\JsonApi\Document\ValueObject\JsonApi;
use Rmk\JsonApi\Document\ValueObject\Link;
use Rmk\JsonApi\Document\ValueObject\Relationship;
use Rmk\JsonApi\Document\ValueObject\Resource;
use Rmk\JsonApi\Document\ValueObject\ResourceIdentifier;
use Rmk\JsonApi\Exception\InvalidPlainObjectException;
use stdClass;

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

    public function testSuccessfulDataBuildFromPlainObjectWithSingleResource(): void
    {
        $object = $this->getPlainObjectWithSingleResourceData();
        $document = DocumentBuilder::fromPlainObject($object)->build();
        $this->assertEquals($object->jsonapi->version, $document->getJsonApi()->getVersion());
        $this->assertSame($object->meta, $document->getMeta());
        /** @var Resource $documentData */
        $documentData = $document->getData();
        $this->assertInstanceOf(Resource::class, $documentData);
        $this->assertEquals($object->data->id, $documentData->getId());
        $this->assertEquals($object->data->type, $documentData->getType());
        $this->assertSame($object->data->attributes, $documentData->getAttributes());
        $documentRelations = $documentData->getRelationships();
        $this->assertEquals(1, $documentRelations->count());
        $this->assertTrue($documentRelations->has('author'));
        /** @var Relationship $author */
        $author = $documentRelations->get('author');
        $this->assertInstanceOf(Relationship::class, $author);
        /** @var Resource $authorData */
        $authorData = $author->getData();
        $this->assertInstanceOf(Resource::class, $authorData);
        $this->assertEquals($object->data->relationships->author->data->id, $authorData->getId());
        $this->assertEquals($object->data->relationships->author->data->type, $authorData->getType());
        $authorLinks = $author->getLinks();
        $this->assertEquals(2, $authorLinks->count());
        $this->assertTrue($authorLinks->has(Link::TYPE_SELF));
        /** @var Link $authorSelfLink */
        $authorSelfLink = $authorLinks->get(Link::TYPE_SELF);
        $this->assertInstanceOf(Link::class, $authorSelfLink);
        $this->assertEquals($object->data->relationships->author->links->self, $authorSelfLink->getHref());
        $this->assertTrue($authorLinks->has(Link::TYPE_RELATED));
        /** @var Link $authorRelatedLink */
        $authorRelatedLink = $authorLinks->get(Link::TYPE_RELATED);
        $this->assertInstanceOf(Link::class, $authorRelatedLink);
        $this->assertEquals($object->data->relationships->author->links->related, $authorRelatedLink->getHref());

        $links = $document->getLinks();
        $this->assertEquals(1, $links->count());
        $this->assertTrue($links->has(Link::TYPE_RELATED));
        /** @var Link $relatedLink */
        $relatedLink = $links->get(Link::TYPE_RELATED);
        $this->assertEquals($object->links->related->href, $relatedLink->getHref());

        $included = $document->getIncluded();
        $this->assertEquals(3, $included->count());
        foreach ($included as $key => $includedResource) {
            /** @var Resource $includedResource */
            $this->assertEquals($object->included[$key]->id, $includedResource->getId());
            $this->assertEquals($object->included[$key]->type, $includedResource->getType());
        }
    }

    public function testSuccessfulDataBuildFromPlainObjectWithMultipleResources(): void
    {
        $object = $this->getPlainObjectWithSingleResourceData();
        $data = clone $object->data;
        $object->data = [$data];
        $document = DocumentBuilder::fromPlainObject($object)->build();
        /** @var ResourcesCollection $documentData */
        $documentData = $document->getData();
        $this->assertInstanceOf(ResourcesCollection::class, $documentData);
        foreach ($documentData as $key => $dataElement) {
            $this->assertEquals($object->data[$key]->id, $dataElement->getId());
            $this->assertEquals($object->data[$key]->type, $dataElement->getType());
            $this->assertSame($object->data[$key]->attributes, $dataElement->getAttributes());
        }
    }

    public function testSuccessfulErrorsBuildFromPlainObject(): void
    {
        $error = new stdClass();
        $error->status =  "422";
        $error->source = new stdClass();
        $error->source->pointer = "/data/attributes/firstName";
        $error->title = "Invalid Attribute";
        $error->detail = "First name must contain at least two characters.";
        $object = $this->getPlainObjectWithSingleResourceData();
        $object->errors = [$error];
        $document = DocumentBuilder::fromPlainObject($object)->build();
        $errors = $document->getErrors();
        $this->assertEquals(1, $errors->count());
        foreach ($errors as $key => $errorElement) {
            /** @var Error $errorElement */
            $this->assertEquals($object->errors[$key]->status, $errorElement->getStatus());
            $this->assertEquals($object->errors[$key]->title, $errorElement->getTitle());
            $this->assertSame($object->errors[$key]->detail, $errorElement->getDetail());
            $this->assertInstanceOf(ErrorSource::class, $errorElement->getSource());
            $this->assertEquals($object->errors[$key]->source->pointer, $errorElement->getSource()->getPointer());
        }
    }

    public function testThrowExceptionWhenBuildDocumentWithoutTypeOrId(): void
    {
        $object = new stdClass();
        $this->expectException(InvalidPlainObjectException::class);
        $this->expectExceptionMessage('A document MUST contain at least one of the "data", "errors" or "meta" top-level members');
        DocumentBuilder::fromPlainObject($object);
    }

    private function getPlainObjectWithSingleResourceData(): stdClass
    {
        $object = new stdClass();
        $object->jsonapi = new stdClass();
        $object->jsonapi->version = JsonApi::VERSION_DEFAULT;
        $object->meta = new stdClass();
        $object->meta->testMetaProperty = 'Test Value';
        $data = new stdClass();
        $data->type = 'test-type';
        $data->id = '123';
        $attributes = new stdClass();
        $attributes->title = 'Test Title';
        $attributes->description = 'Lorem ipsum...';
        $data->attributes = $attributes;
        $relationships = new stdClass();
        $relationships->author = new stdClass();
        $relationships->author->links = new stdClass();
        $relationships->author->links->self = "https://example.com/articles/1/relationships/author";
        $relationships->author->links->related = "https://example.com/articles/1/author";
        $relationships->author->data = new stdClass();
        $relationships->author->data->type = "people";
        $relationships->author->data->id = "9";
        $data->relationships = $relationships;
        // add data...
        $object->data = $data;
        $object->links = new stdClass();
        $object->links->related = new stdClass();
        $object->links->related->href = "https://example.com/articles/1/comments";
        $object->links->related->meta = new stdClass();
        $object->links->related->meta->count = 10;

        $includedJsonString = '[{
    "type": "people",
    "id": "9",
    "attributes": {
      "first-name": "Dan",
      "last-name": "Gebhardt",
      "twitter": "dgeb"
    },
    "links": {
      "self": "https://example.com/people/9"
    }
  }, {
    "type": "comments",
    "id": "5",
    "attributes": {
      "body": "First!"
    },
    "relationships": {
      "author": {
        "data": { "type": "people", "id": "2" }
      }
    },
    "links": {
      "self": "https://example.com/comments/5"
    }
  }, {
    "type": "comments",
    "id": "12",
    "attributes": {
      "body": "I like XML better"
    },
    "relationships": {
      "author": {
        "data": { "type": "people", "id": "9" }
      }
    },
    "links": {
      "self": "https://example.com/comments/12"
    }
  }]';
        $object->included = json_decode($includedJsonString);

        return $object;
    }
}
