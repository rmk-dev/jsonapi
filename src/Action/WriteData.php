<?php

namespace Rmk\JsonApi\Action;

use Rmk\JsonApi\Action\Collection\WritersCollection;
use Rmk\JsonApi\Contracts\ResourceWriter;
use Rmk\JsonApi\Document\Builder\ResourceBuilder;
use Rmk\JsonApi\Document\ValueObject\Resource;
use Rmk\JsonApi\Dto\CommandParameters;
use Rmk\JsonApi\Exception\ResourceWritingException;
use Rmk\JsonApi\Exception\UnsupportedResourceTypeException;

/**
 * Performs writing data to data source
 */
class WriteData
{

    /**
     * Collection with data writer objects
     *
     * @var WritersCollection<string, ResourceWriter>
     */
    protected WritersCollection $writers;

    /**
     * @param WritersCollection $writers
     */
    public function __construct(WritersCollection $writers)
    {
        $this->writers = $writers;
    }

    /**
     * Performs the data writing on the base of the query parameters
     *
     * This method will try to find the proper data writer and will pass the data to it for writing. If no writer is
     * found for the type, it will throw UnsupportedResourceTypeException. If the writing failed it will throw
     * ResourceWritingException that contains the original exception that caused the failure.
     *
     * @param CommandParameters $parameters
     *
     * @return Resource
     *
     * @throws UnsupportedResourceTypeException
     * @throws ResourceWritingException
     */
    public function execute(CommandParameters $parameters): Resource
    {
        $type = $parameters->getType();
        if (!$this->writers->has($type)) {
            throw new UnsupportedResourceTypeException('No writer is found for resource type: ' . $type);
        }
        /** @var ResourceWriter $writer */
        $writer = $this->writers->get($type);
        try {
            $resourceBuilder = ResourceBuilder::fromPlainObject($parameters->getData());

            return $writer->write($resourceBuilder->build());
        } catch (\Throwable $exception) {
            throw new ResourceWritingException('Resource cannot be written', 1, $exception);
        }
    }
}
