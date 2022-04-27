<?php

namespace RmkTests\JsonApi;

use PHPUnit\Framework\TestCase;
use Rmk\JsonApi\Document\Builder\ResourceBuilder;
use Rmk\JsonApi\Document\Collection\LinksCollection;
use Rmk\JsonApi\Document\Collection\RelationshipsCollection;
use Rmk\JsonApi\Document\ValueObject\Link;
use Rmk\JsonApi\Document\ValueObject\Relationship;
use Rmk\JsonApi\Document\ValueObject\Resource;
use Rmk\JsonApi\Document\ValueObject\ResourceIdentifier;

class ResourceBuilderTest extends TestCase
{

    public function testBuildIdentifier(): void
    {
        $identifier = ResourceBuilder::instance()
            ->withId('123')
            ->withType('test-type')
            ->buildIdentifier();

        $this->assertNotInstanceOf(Resource::class, $identifier);
        $this->assertEquals('123', $identifier->getId());
        $this->assertEquals('test-type', $identifier->getType());
    }

    public function testBuildFromIdentifier(): void
    {
        $proto = new ResourceIdentifier('123', 'test-type');
        $identifier = ResourceBuilder::fromResourceIdentifier($proto)
            ->withType('new-type')
            ->buildIdentifier();

        $this->assertEquals($proto->getId(), $identifier->getId());
        $this->assertNotEquals($proto->getType(), $identifier->getType());
        $this->assertEquals('new-type', $identifier->getType());
    }

    public function testBuildResource(): void
    {
        $attributes = new \stdClass();
        $meta = new \stdClass();
        $meta->author = 'Test User';
        $links = new LinksCollection();
        $additionalLink = new Link('https://example.com');
        $rels = new RelationshipsCollection();
        $additionalRel = new Relationship();
        $resource = ResourceBuilder::instance()
            ->withType('test-type')
            ->withId('123')
            ->withAttributes($attributes)
            ->withMeta($meta)
            ->withLinks($links)
            ->withRelations($rels)
            ->withLink($additionalLink, Link::TYPE_ABOUT)
            ->withRelation($additionalRel, 'test-rel')
            ->buildResource();

        $this->assertEquals('123', $resource->getId());
        $this->assertEquals('test-type', $resource->getType());
        $this->assertSame($attributes, $resource->getAttributes());
        $this->assertSame($meta, $resource->getMeta());
        $this->assertSame($links, $resource->getLinks());
        $this->assertSame($rels, $resource->getRelationships());
        $this->assertTrue($links->contains($additionalLink));
        $this->assertTrue($rels->contains($additionalRel));
    }

    public function testBuildFromResource(): void
    {
        $proto = new Resource('123', 'test-type');
        $resource = ResourceBuilder::fromResource($proto)->buildResource();
        $this->assertNotSame($proto, $resource);
        $this->assertEquals($proto->getId(), $resource->getId());
        $this->assertEquals($proto->getType(), $resource->getType());
    }
}
