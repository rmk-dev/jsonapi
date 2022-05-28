<?php

namespace RmkTests\JsonApi\Unit\Http;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Rmk\JsonApi\Exception\ContentTypeException;
use Rmk\JsonApi\Http\ContentType;

class ContentTypeTest extends TestCase
{

    public function testRequestSendsJsonApi(): void
    {
        $request = $this->createMock(RequestInterface::class);
        $request->expects($this->once())
            ->method('getHeaderLine')
            ->willReturn(ContentType::JSON_API_TYPE);

        ContentType::assertRequestSendsJsonApi($request);
    }

    public function testRequestFailsToSendJsonApi(): void
    {
        $request = $this->createMock(RequestInterface::class);
        $request->expects($this->once())
            ->method('getHeaderLine')
            ->willReturn(ContentType::JSON_API_TYPE.'; charset=utf-8');

        $this->expectException(ContentTypeException::class);
        $this->expectExceptionMessage('Invalid "Content-Type" header');
        $this->expectExceptionCode(ContentTypeException::INVALID_CONTENT_TYPE_HEADER);
        ContentType::assertRequestSendsJsonApi($request);
    }

    public function testRequestAcceptsJsonApi(): void
    {
        $request = $this->createMock(RequestInterface::class);
        $request->expects($this->once())
            ->method('getHeaderLine')
            ->willReturn(ContentType::JSON_API_TYPE . ', text/xml; charset=utf-8');

        ContentType::assertRequestAcceptsJsonApi($request);
    }


    public function testRequestFailsToAcceptJsonApi(): void
    {
        $request = $this->createMock(RequestInterface::class);
        $request->expects($this->once())
            ->method('getHeaderLine')
            ->willReturn(ContentType::JSON_API_TYPE.'; charset=utf-8');

        $this->expectException(ContentTypeException::class);
        $this->expectExceptionMessage('The "Accept" header must not contains media type parameters');
        $this->expectExceptionCode(ContentTypeException::INVALID_ACCEPT_HEADER);
        ContentType::assertRequestAcceptsJsonApi($request);
    }
}