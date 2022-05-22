<?php

namespace RmkTests\JsonApi\Action;

use PHPUnit\Framework\TestCase;
use Rmk\JsonApi\Action\Collection\WritersCollection;
use Rmk\JsonApi\Action\WriteData;
use Rmk\JsonApi\Contracts\ResourceWriter;
use Rmk\JsonApi\Document\ValueObject\ResourceIdentifier;
use Rmk\JsonApi\Dto\CommandParameters;
use Rmk\JsonApi\Exception\ResourceWritingException;
use Rmk\JsonApi\Exception\UnsupportedResourceTypeException;

class  WriteDataTest extends TestCase
{

    private WriteData $writer;

    protected function setUp(): void
    {
        $writer1 = $this->getMockForAbstractClass(ResourceWriter::class);
        $writer1->expects($this->atMost(1))->method('write')->willReturnArgument(0);
        $writer2 = $this->getMockForAbstractClass(ResourceWriter::class);
        $writer2->method('write')->willThrowException(new \RuntimeException('Writing failed'));
        $writers = new WritersCollection();
        $writers->set('success', $writer1);
        $writers->set('fail', $writer2);
        $this->writer = new WriteData($writers);
    }

    public function testSuccessfullyWriting(): void
    {
        $data = new \stdClass();
        $data->type = 'success';
        $params = new CommandParameters('success', $data);
        $resource = $this->writer->execute($params);
        $this->assertEquals(ResourceIdentifier::EMPTY_ID, $resource->getId());
    }

    public function testWritingFailedException(): void
    {
        $data = new \stdClass();
        $data->type = 'fail';
        $params = new CommandParameters('fail', $data);
        $this->expectException(ResourceWritingException::class);
        $this->expectExceptionMessage('Resource cannot be written');
        $this->writer->execute($params);
    }

    public function testUnsupportedResourceTypeException(): void
    {
        $data = new \stdClass();
        $data->type = 'undefined_type';
        $params = new CommandParameters('undefined_type', $data);
        $this->expectException(UnsupportedResourceTypeException::class);
        $this->expectExceptionMessage('No writer is found for resource type: undefined_type');
        $this->writer->execute($params);
    }
}