<?php

namespace RmkTests\JsonApi\Builder;

use PHPUnit\Framework\TestCase;
use Rmk\JsonApi\Document\Builder\ErrorBuilder;

class ErrorBuilderTest extends TestCase
{

    public function testBuildFromPlainObject(): void
    {
        $plain = new \stdClass();
        $plain->id = '123';
        $plain->status = 400;
        $plain->title = 'Bad Request';
        $plain->code = 4;
        $plain->detail = 'Test detail';
        $plain->link = 'https://example.com/errors/4';
        $plain->source = new \stdClass();
        $plain->source->pointer = '$.json.path.pointer';
        $plain->source->parameter = 'some param';
        $plain->meta = new \stdClass();
        $plain->meta->test = 'alabala';
        $error = ErrorBuilder::fromPlainObject($plain)->build();
        $this->assertSame($plain->meta, $error->getMeta());
        $this->assertEquals($plain->id, $error->getId());
        $this->assertEquals($plain->status, $error->getStatus());
        $this->assertEquals($plain->title, $error->getTitle());
        $this->assertEquals($plain->code, $error->getCode());
        $this->assertEquals($plain->detail, $error->getDetail());
        $this->assertEquals($plain->link, $error->getLink()->getHref());
        $this->assertEquals($plain->source->pointer, $error->getSource()->getPointer());
        $this->assertEquals($plain->source->parameter, $error->getSource()->getParameter());
    }
}
