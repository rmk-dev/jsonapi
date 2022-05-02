<?php

namespace Rmk\JsonApi\Exception;

use Exception;
use Throwable;

class ResourceNotFoundException extends Exception
{

    private string $id;

    private string $type;

    public function __construct($id, $type, $code = 0, Throwable $previous = null)
    {
        $this->id = $id;
        $this->type = $type;
        $message = 'Resource with ID %s of type %s is not found';
        parent::__construct(sprintf($message, $id, $type), $code, $previous);
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }
}
