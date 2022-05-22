<?php

namespace Rmk\JsonApi\Dto;

use stdClass;

/**
 * Parameters for performing data-changeable actions (CREATE, UPDATE)
 */
class CommandParameters
{

    protected string $type = '';

    protected stdClass $data;

    /**
     * @param string $type
     * @param stdClass $data
     */
    public function __construct(string $type, stdClass $data)
    {
        $this->setType($type);
        $this->setData($data);
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return CommandParameters
     */
    public function setType(string $type): CommandParameters
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return stdClass
     */
    public function getData(): stdClass
    {
        return $this->data;
    }

    /**
     * @param stdClass $data
     *
     * @return CommandParameters
     */
    public function setData(stdClass $data): CommandParameters
    {
        $this->data = $data;
        return $this;
    }
}
