<?php

namespace RmkTests\JsonApi\Unit\Builder;

use PHPUnit\Framework\TestCase;
use Rmk\JsonApi\Contracts\DocumentData;
use Rmk\JsonApi\Document\Builder\RelationshipBuilder;
use Rmk\JsonApi\Document\Collection\LinksCollection;
use Rmk\JsonApi\Document\ValueObject\Link;
use Rmk\JsonApi\Document\ValueObject\ResourceIdentifier;

class RelationshipBuilderTest extends TestCase
{

    public function testDefaultBuild(): void
    {
        $links = new LinksCollection();
        $data = new class implements DocumentData {
            public function count() { return 1; }
            public function jsonSerialize() { return []; }
        };
        $meta = new \stdClass();
        $relation = RelationshipBuilder::instance()
            ->withLinks($links)
            ->withData($data)
            ->withMeta($meta)
            ->build();
        $this->assertSame($links, $relation->getLinks());
        $this->assertSame($data, $relation->getData());
        $this->assertSame($meta, $relation->getMeta());
    }

    public function testBuildFromPlainObject(): void
    {
        $plain = new \stdClass();
        $plain->links = new \stdClass();
        $plain->links->self = "https://example.com/articles/1/relationships/author";
        $plain->links->related = "https://example.com/articles/1/author";
        $plain->data = new \stdClass();
        $plain->data->type = "people";
        $plain->data->id = "9";
        $plain->meta = new \stdClass();
        $plain->meta->test = "value";
        $relation = RelationshipBuilder::fromPlainObject($plain)->build();
        $this->assertInstanceOf(ResourceIdentifier::class, $relation->getData());
        $this->assertEquals($plain->data->type, $relation->getData()->getType());
        $this->assertEquals($plain->data->id, $relation->getData()->getId());
        $this->assertInstanceOf(LinksCollection::class, $relation->getLinks());
        $this->assertEquals(2, $relation->getLinks()->count());
        $this->assertEquals($plain->links->self, $relation->getLinks()->get(Link::TYPE_SELF)->getHref());
        $this->assertEquals($plain->links->related, $relation->getLinks()->get(Link::TYPE_RELATED)->getHref());
        $this->assertSame($plain->meta, $relation->getMeta());
    }
}
