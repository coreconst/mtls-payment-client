<?php

namespace Coreconst\MtlsPaymentClient\Tests\Unit;

use Coreconst\MtlsPaymentClient\Exceptions\UnexpectedStatusCodeException;
use Coreconst\MtlsPaymentClient\Http\Validation\ResponseValidator;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class ResponseValidatorTest extends TestCase
{
    public function testValidateDoesNothingForSuccessfulResponse(): void
    {
        $response = $this->createMock(ResponseInterface::class);
        $response
            ->method('getStatusCode')
            ->willReturn(200);

        $validator = new ResponseValidator();

        $validator->validate($response);

        $this->assertTrue(true); // if no exception is thrown, the test passes
    }

    public function testValidateThrowsForUnexpectedStatusCode(): void
    {
        $response = $this->createMock(ResponseInterface::class);
        $response
            ->method('getStatusCode')
            ->willReturn(500);

        $stream = $this->createMock(StreamInterface::class);
        $stream
            ->method('__toString')
            ->willReturn('Internal Server Error');

        $response
            ->method('getBody')
            ->willReturn($stream);

        $validator = new ResponseValidator();

        $this->expectException(UnexpectedStatusCodeException::class);

        $validator->validate($response);
    }
}

