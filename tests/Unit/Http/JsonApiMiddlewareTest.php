<?php

namespace RmkTests\JsonApi\Unit\Http;

use GuzzleHttp\Psr7\Response;
use Http\Factory\Guzzle\ResponseFactory;
use Http\Factory\Guzzle\ServerRequestFactory;
use Http\Factory\Guzzle\StreamFactory;
use PHPUnit\Framework\TestCase;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Rmk\JsonApi\Document\Builder\ErrorBuilder;
use Rmk\JsonApi\Document\ValueObject\Link;
use Rmk\JsonApi\Exception\ContentTypeException;
use Rmk\JsonApi\Http\ContentType;
use Rmk\JsonApi\Http\JsonApiMiddleware;
use stdClass;

class JsonApiMiddlewareTest extends TestCase
{

    private JsonApiMiddleware $middleware;

    private RequestHandlerInterface $handler;

    protected function setUp(): void
    {
        $this->handler = new class implements RequestHandlerInterface {
            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                return new Response();
            }
        };
        $this->middleware = new JsonApiMiddleware(new ResponseFactory(), new StreamFactory());
    }

    public function testSendValidJsonApiRequest(): void
    {
        $requestFactory = new ServerRequestFactory();
        $request = $requestFactory->createServerRequest('GET', 'https://example.com');
        $response = $this->middleware->process($request, $this->handler);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('OK', $response->getReasonPhrase());
        $this->assertEquals(ContentType::JSON_API_TYPE, $response->getHeaderLine('Content-Type'));
    }

    public function testSendInvalidRequestMethod(): void
    {
        $requestFactory = new ServerRequestFactory();
        $request = $requestFactory->createServerRequest('PUT', 'https://example.com');
        $response = $this->middleware->process($request, $this->handler);
        $this->assertEquals(405, $response->getStatusCode());
        $this->assertEquals('Method Not Allowed', $response->getReasonPhrase());
        $this->assertEmpty($response->getBody() . '');
    }

    public function testSendInvalidCContentTypeHeader(): void
    {
        $requestFactory = new ServerRequestFactory();
        $request = $requestFactory->createServerRequest('POST', 'https://example.com');
        $response = $this->middleware->process($request->withHeader('Content-Type', 'text/html'), $this->handler);
        $body = json_decode($response->getBody() . '');
        $this->assertEquals(415, $response->getStatusCode());
        $this->assertEquals('Unsupported Media Type', $response->getReasonPhrase());
        $this->assertInstanceOf(stdClass::class, $body);
        $this->assertNotEmpty($body->errors);
        $this->assertIsArray($body->errors);
        $this->assertArrayHasKey(0, $body->errors);
        $plain = $body->errors[0];
        $error = ErrorBuilder::fromPlainObject($plain)->build();
        $this->assertEquals('415', $error->getStatus());
        $this->assertEquals('Invalid "Content-Type" header', $error->getTitle());
        $this->assertEquals(ContentTypeException::INVALID_CONTENT_TYPE_HEADER, $error->getCode());
        $this->assertEquals(JsonApiMiddleware::CONTENT_TYPE_URL, $error->getLink()[Link::TYPE_ABOUT]);
    }

    public function testSendInvalidAcceptHeader(): void
    {
        $requestFactory = new ServerRequestFactory();
        $request = $requestFactory->createServerRequest('GET', 'https://example.com', );
        $response = $this->middleware->process($request->withHeader('Accept', ContentType::JSON_API_TYPE . '; charset=utf-8'), $this->handler);
        $body = json_decode($response->getBody() . '');
        $this->assertEquals(406, $response->getStatusCode());
        $this->assertEquals('Not Acceptable', $response->getReasonPhrase());
        $this->assertInstanceOf(stdClass::class, $body);
        $this->assertNotEmpty($body->errors);
        $this->assertIsArray($body->errors);
        $this->assertArrayHasKey(0, $body->errors);
        $plain = $body->errors[0];
        $error = ErrorBuilder::fromPlainObject($plain)->build();
        $this->assertEquals('406', $error->getStatus());
        $this->assertEquals('The "Accept" header must not contains media type parameters', $error->getTitle());
        $this->assertEquals(ContentTypeException::INVALID_ACCEPT_HEADER, $error->getCode());
        $this->assertEquals(JsonApiMiddleware::ACCEPT_URL, $error->getLink()[Link::TYPE_ABOUT]);
    }

    public function testReceiveValidJsonApiResponse(): void
    {
        $streamFactory = new StreamFactory();
        $requestFactory = new ServerRequestFactory();
        $request = $requestFactory->createServerRequest('PATCH', 'https://example.com');
        $request = $request->withBody($streamFactory->createStream(json_encode(['a' => 1])));
        $response = $this->middleware->process($request->withHeader('Content-Type', ContentType::JSON_API_TYPE), $this->handler);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('OK', $response->getReasonPhrase());
        $this->assertEquals(ContentType::JSON_API_TYPE, $response->getHeaderLine('Content-Type'));
    }

    public function testReceiveInvalidJsonApiResponse(): void
    {
        $streamFactory = new StreamFactory();
        $requestFactory = new ServerRequestFactory();
        $request = $requestFactory->createServerRequest('PATCH', 'https://example.com');
        $request = $request->withBody($streamFactory->createStream('aaa'));
        $this->expectException(\JsonException::class);
        $this->middleware->process($request->withHeader('Content-Type', ContentType::JSON_API_TYPE), $this->handler);
    }
}
