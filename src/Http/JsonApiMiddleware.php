<?php

namespace Rmk\JsonApi\Http;

use JsonException;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Rmk\JsonApi\Document\Builder\DocumentBuilder;
use Rmk\JsonApi\Document\Builder\ErrorBuilder;
use Rmk\JsonApi\Document\Collection\ErrorsCollection;
use Rmk\JsonApi\Document\ValueObject\Document;
use Rmk\JsonApi\Exception\ContentTypeException;
use stdClass;

/**
 * Middleware for checking JSON API requests and responses
 */
class JsonApiMiddleware implements MiddlewareInterface
{

    /**
     * Allowed HTTP methods
     */
    public const ALLOWED_METHODS = ['get', 'post', 'patch', 'delete'];

    /**
     * Special HTTP methods for checks
     */
    public const SPECIAL_METHODS = ['head' ,'options'];

    /**
     * HTTP methods that sends data
     */
    public const METHODS_WITH_BODY = ['post', 'patch'];

    /**
     * Documentation link about clients responsibilities
     */
    public const CONTENT_TYPE_URL = 'https://jsonapi.org/format/#content-negotiation-clients';

    /**
     * Documentation link about servers responsibilities
     */
    public const ACCEPT_URL = 'https://jsonapi.org/format/#content-negotiation-servers';

    /**
     * Documentation link about JSON API at general
     */
    public const DOCS_URL = 'https://jsonapi.org/format/#introduction';

    /**
     * Factory for responses
     *
     * @var ResponseFactoryInterface
     */
    protected ResponseFactoryInterface $responseFactory;

    /**
     * Factory for streams
     *
     * @var StreamFactoryInterface
     */
    protected StreamFactoryInterface $streamFactory;

    /**
     * Create new JSON-API middleware
     *
     * @param ResponseFactoryInterface $responseFactory
     * @param StreamFactoryInterface $streamFactory
     */
    public function __construct(ResponseFactoryInterface $responseFactory, StreamFactoryInterface $streamFactory)
    {
        $this->responseFactory = $responseFactory;
        $this->streamFactory = $streamFactory;
    }

    /**
     * Process the sent request and pass it to the handler if everything is OK. It will check the response too.
     *
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     *
     * @throws JsonException
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // check request...
        $method = strtolower($request->getMethod());
        if (!in_array($method, self::ALLOWED_METHODS) && !in_array($method, self::SPECIAL_METHODS)) {
            return $this->responseFactory->createResponse(405, 'Method Not Allowed');
        }
        try {
            if (in_array($method, self::METHODS_WITH_BODY)) {
                ContentType::assertContentTypeIsJsonApi($request);
                $request = $request->withParsedBody(json_decode($request->getBody()->__toString(), false, 512, JSON_THROW_ON_ERROR));
            }
            ContentType::assertRequestAcceptsJsonApi($request);
            $response = $handler->handle($request);
        } catch (ContentTypeException $exception) {
            $response = $this->createResponseFromException($exception);
        }

        return $response->withHeader('Content-Type', ContentType::JSON_API_TYPE);
    }

    /**
     * Creata a response from content-type exception
     *
     * @param ContentTypeException $exception
     *
     * @return ResponseInterface
     */
    protected function createResponseFromException(ContentTypeException $exception): ResponseInterface
    {
        $elements = $this->prepareErrorElements($exception);
        $exception->setLink($elements->url);
        $exception->setHttpStatus($elements->code);
        $document = $this->prepareDocument($exception);
        $response = $this->responseFactory->createResponse($elements->code, $elements->phrase);

        return $response->withBody($this->streamFactory->createStream(json_encode($document)));
    }

    /**
     * Prepare the document for the response body
     *
     * @param ContentTypeException $exception
     *
     * @return Document
     */
    protected function prepareDocument(ContentTypeException $exception): Document
    {
        $error = ErrorBuilder::fromThrowable($exception)->build();
        $errors = new ErrorsCollection([$error]);

        return DocumentBuilder::instance()->withData($errors)->build();
    }

    /**
     * Define the error elements
     *
     * @param ContentTypeException $exception
     *
     * @return stdClass
     */
    protected function prepareErrorElements(ContentTypeException $exception): stdClass
    {
        $elements = new stdClass();
        $elements->code = 400;
        $elements->phrase = 'Bad Request';
        $elements->url = self::DOCS_URL;

        switch ($exception->getCode()) {
            case ContentTypeException::INVALID_CONTENT_TYPE_HEADER:
                $elements->code = 415;
                $elements->phrase = 'Unsupported Media Type';
                $elements->url = self::CONTENT_TYPE_URL;
                break;

            case ContentTypeException::INVALID_ACCEPT_HEADER:
                $elements->code = 406;
                $elements->phrase = 'Not Acceptable';
                $elements->url = self::ACCEPT_URL;
                break;
        }

        return $elements;
    }
}
