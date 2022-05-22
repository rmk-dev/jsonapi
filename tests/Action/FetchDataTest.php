<?php

namespace RmkTests\JsonApi\Action;

use PHPUnit\Framework\TestCase;
use Rmk\Collections\Collection;
use Rmk\JsonApi\Action\Collection\ReadersCollection;
use Rmk\JsonApi\Action\FetchData;
use Rmk\JsonApi\Contracts\Relationship as RelationshipContract;
use Rmk\JsonApi\Contracts\ResourceReader;
use Rmk\JsonApi\Document\Collection\ResourcesCollection;
use Rmk\JsonApi\Document\ValueObject\Relationship;
use Rmk\JsonApi\Document\ValueObject\Resource;
use Rmk\JsonApi\Dto\PaginationParameters;
use Rmk\JsonApi\Dto\QueryParameters;
use Rmk\JsonApi\Exception\RelationshipDoesNotExistsException;
use Rmk\JsonApi\Exception\ResourceNotFoundException;
use Rmk\JsonApi\Exception\ResourceReadingException;
use Rmk\JsonApi\Exception\UnsupportedResourceTypeException;

class FetchDataTest extends TestCase
{

    private FetchData $action;

    protected function setUp(): void
    {
        $reader = $this->createMock(ResourceReader::class);
        $reader->expects($this->atMost(1))
            ->method('readCollection')
            ->willReturn(new ResourcesCollection());

        $reader->expects($this->atMost(1))
            ->method('read')
            ->willReturn(new Resource(1, 'test_type'));

        $reader->expects($this->atMost(1))
            ->method('readRelation')
            ->willReturn(new Relationship());

        $failedReader = $this->createMock(ResourceReader::class);
        $failedReader->expects($this->atMost(1))
            ->method('read')->willThrowException(new \RuntimeException('Failed to read'));

        $notFoundReader = $this->createMock(ResourceReader::class);
        $notFoundReader->expects($this->atMost(1))
            ->method('read')->willThrowException(new ResourceNotFoundException(1, 'not_found'));

        $relationNotExistsReader = $this->createMock(ResourceReader::class);
        $relationNotExistsReader->expects($this->atMost(1))
            ->method('readRelation')->willThrowException(new RelationshipDoesNotExistsException('rel_not_found', 'some_name'));

        $collection = new ReadersCollection([
            'test_type' => $reader,
            'fail' => $failedReader,
            'not_found' => $notFoundReader,
            'rel_not_found' => $relationNotExistsReader,
        ]);

        $this->action = new FetchData($collection);
    }

    public function testFetchResourceCollection(): void
    {
        $resourceCollection = $this->action->execute(new QueryParameters('', 'test_type'), new PaginationParameters());
        $this->assertInstanceOf(ResourcesCollection::class, $resourceCollection);
    }

    public function testFetchSingleResource(): void
    {
        $resource = $this->action->execute(new QueryParameters(1, 'test_type'), new PaginationParameters());
        $this->assertInstanceOf(Resource::class, $resource);
    }

    public function testFetchRelationCollection(): void
    {
        $rel = $this->action->execute(new QueryParameters(1, 'test_type', 'rel'), new PaginationParameters());
        $this->assertInstanceOf(RelationshipContract::class, $rel);
    }

    public function testUnsupportedResourceTypeException(): void
    {
        $this->expectException(UnsupportedResourceTypeException::class);
        $this->expectExceptionMessage('No reader is found for resource type: undefined');
        $this->action->execute(new QueryParameters(1, 'undefined'), new PaginationParameters());
    }

    public function testResourceReadingException(): void
    {
        $this->expectException(ResourceReadingException::class);
        $this->expectExceptionMessage('Resource cannot be read');
        $this->action->execute(new QueryParameters(1, 'fail'), new PaginationParameters());
    }

    public function testResourceNotFoundException(): void
    {
        $this->expectException(ResourceNotFoundException::class);
        $this->action->execute(new QueryParameters(1, 'not_found'), new PaginationParameters());
    }

    public function testRelationshipDoesNotExistsException(): void
    {
        $this->expectException(RelationshipDoesNotExistsException::class);
        $this->action->execute(new QueryParameters(1, 'rel_not_found', 'undefined'), new PaginationParameters());
    }
}
