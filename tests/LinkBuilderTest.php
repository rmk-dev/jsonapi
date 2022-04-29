<?php

namespace RmkTests\JsonApi;

use PHPUnit\Framework\TestCase;
use Rmk\JsonApi\Document\Builder\LinkBuilder;
use Rmk\JsonApi\Document\ValueObject\Link;
use Rmk\JsonApi\Exception\InvalidPlainObjectException;
use stdClass;

class LinkBuilderTest extends TestCase
{

    public function testBuilderMethods(): void
    {
        $meta = new stdClass();
        $meta->author = 'Test Author';
        $link = LinkBuilder::instance()
            ->withHref('https://example.com')
            ->withMeta($meta)
            ->build();

        $this->assertEquals('https://example.com', $link->getHref());
        $this->assertSame($meta, $link->getMeta());
    }

    public function testBuildFromLink(): void
    {
        $meta = new stdClass();
        $meta->author = 'Test Author';
        $protoLink = new Link('https://example.com', $meta);
        $link = LinkBuilder::fromLink($protoLink)
            ->withHref('https://example.org')
            ->build();

        $this->assertNotEquals($protoLink->getHref(), $link->getHref());
        $this->assertSame($meta, $link->getMeta());
    }

    public function testBuildFromPlainObject(): void
    {
        $plain = new stdClass();
        $plain->href = 'https://example.com';
        $plain->meta = new stdClass();
        $plain->meta->author = 'Test Author';
        $link = LinkBuilder::fromPlainObject($plain)->build();
        $this->assertEquals($plain->href, $link->getHref());
        $this->assertSame($plain->meta, $link->getMeta());
    }

    public function testFailBuildFromPlainWithInvalidObject(): void
    {
        $this->expectException(InvalidPlainObjectException::class);
        $this->expectExceptionMessage('Link must be either a stdClass instance or string');
        LinkBuilder::fromPlainObject(123);
    }
}
