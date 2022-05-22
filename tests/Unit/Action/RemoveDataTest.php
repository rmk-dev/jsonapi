<?php

namespace RmkTests\JsonApi\Unit\Action;

use PHPUnit\Framework\TestCase;
use Rmk\JsonApi\Action\Collection\RemoversCollection;
use Rmk\JsonApi\Action\RemoveData;
use Rmk\JsonApi\Contracts\ResourceRemover;
use Rmk\JsonApi\Exception\ResourceNotFoundException;
use Rmk\JsonApi\Exception\ResourceRemovingException;
use Rmk\JsonApi\Exception\UnsupportedResourceTypeException;
use RuntimeException;

class RemoveDataTest extends TestCase
{

    private RemoveData $action;

    protected function setUp(): void
    {
        $remover1 = $this->createMock(ResourceRemover::class);
        $remover1->method('remove')->willReturn(true);
        $remover2 = $this->createMock(ResourceRemover::class);
        $remover2->method('remove')->willThrowException(new RuntimeException('Remove failed'));
        $remover3 = $this->createMock(ResourceRemover::class);
        $remover3->method('remove')->willThrowException(new ResourceNotFoundException('123', 'not_found'));

        $this->action = new RemoveData(new RemoversCollection([
            'success' => $remover1,
            'fail' => $remover2,
            'not_found' => $remover3,
        ]));
    }

    public function testSuccessfullyRemoveResource(): void
    {
        $this->assertTrue($this->action->execute('success', 1));
    }

    public function testUnsupportedResourceTypeException(): void
    {
        $this->expectException(UnsupportedResourceTypeException::class);
        $this->expectExceptionMessage('Resource with type undefined is not supported for removing');
        $this->action->execute('undefined', '123');
    }

    public function testResourceNotFoundException(): void
    {
        $this->expectException(ResourceNotFoundException::class);
        $this->expectExceptionMessage('Resource with ID 123 of type not_found is not found');
        $this->action->execute('not_found', '123');
    }

    public function testRemoveFailedException(): void
    {
        $this->expectException(ResourceRemovingException::class);
        $this->expectExceptionMessage('Resource is not removed');
        $this->action->execute('fail', '123');
    }
}
