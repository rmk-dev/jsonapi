<?php

namespace Rmk\JsonApi\Exception;

use Exception;
use Throwable;

class RelationshipDoesNotExistsException extends Exception
{

    /**
     * @var string
     */
    private string $type;

    /**
     * @var string
     */
    private string $name;

    public function __construct(string $type, string $name, $code = 0, Throwable $previous = null)
    {
        $this->type = $type;
        $this->name = $name;
        $message = 'Resource of type %s does not have a relationship with name %s';
        parent::__construct(sprintf($message, $type, $name), $code, $previous);
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}
