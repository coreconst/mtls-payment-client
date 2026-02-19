<?php

namespace Coreconst\MtlsPaymentClient\Exceptions;

use Coreconst\MtlsPaymentClient\Exceptions\MtlsClientException;

class UnexpectedStatusCodeException extends MtlsClientException
{
    public function __construct(
        private readonly int $statusCode,
        private readonly string $responseBody,
    ) {
        parent::__construct(
            sprintf(
                'Unexpected HTTP status code: %d. Body: %s',
                $statusCode,
                $responseBody
            )
        );
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getResponseBody(): string
    {
        return $this->responseBody;
    }
}