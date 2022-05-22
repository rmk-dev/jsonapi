<?php

namespace RmkTests\JsonApi\Action;

use PHPUnit\Framework\TestCase;
use Rmk\Collections\Collection;
use Rmk\JsonApi\Action\FetchData;
use Rmk\JsonApi\Contracts\Relationship as RelationshipContract;
use Rmk\JsonApi\Contracts\ResourceReader;
use Rmk\JsonApi\Document\Collection\ResourcesCollection;
use Rmk\JsonApi\Document\ValueObject\Relationship;
use Rmk\JsonApi\Document\ValueObject\Resource;
use Rmk\JsonApi\Dto\PaginationParameters;
use Rmk\JsonApi\Dto\QueryParameters;

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

        $collection = new Collection(['test_type' => $reader]);

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
}
