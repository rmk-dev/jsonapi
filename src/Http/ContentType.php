<?php

namespace Rmk\JsonApi\Http;

use Psr\Http\Message\RequestInterface;
use Rmk\JsonApi\Exception\ContentTypeException;

class ContentType
{

    public const JSON_API_TYPE = 'application/vnd.api+json';

    /**
     * @param RequestInterface $request
     *
     * @return void
     *
     * @throws ContentTypeException
     */
    public function assertRequestSendsJsonApi(RequestInterface $request): void
    {
        $header = $request->getHeaderLine('Content-Type');
        if ($header !== self::JSON_API_TYPE) {
            throw new ContentTypeException(
                'Invalid "Content-Type" header',
                ContentTypeException::INVALID_CONTENT_TYPE_HEADER
            );
        }
    }

    /**
     * @param RequestInterface $request
     *
     * @return void
     *
     * @throws ContentTypeException
     */
    public function assertRequestAcceptsJsonApi(RequestInterface $request): void
    {
        $header = $request->getHeaderLine('Accept');
        $regex = sprintf('/%s(,.+)?$/', str_replace('/', '\\/', preg_quote(self::JSON_API_TYPE)));;
        if (strpos($header, self::JSON_API_TYPE) !== false && !preg_match($regex, $header)) {
            throw new ContentTypeException(
                'The "Accept" header must not contains media type parameters',
                ContentTypeException::INVALID_ACCEPT_HEADER
            );
        }
    }
}
