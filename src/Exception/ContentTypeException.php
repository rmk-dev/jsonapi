<?php

namespace Rmk\JsonApi\Exception;

use Exception;

class ContentTypeException extends Exception
{

    public const INVALID_CONTENT_TYPE_HEADER = 1;

    public const INVALID_ACCEPT_HEADER = 2;
}
