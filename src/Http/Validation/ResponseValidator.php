<?php

namespace Coreconst\MtlsPaymentClient\Http\Validation;

use Psr\Http\Message\ResponseInterface;
use Coreconst\MtlsPaymentClient\Http\Validation\Contracts\ValidatorInterface;
use Coreconst\MtlsPaymentClient\Exceptions\UnexpectedStatusCodeException;

class ResponseValidator implements ValidatorInterface
{
    public function validate(ResponseInterface $response): void
    {
        $statusCode = $response->getStatusCode();

        if ($statusCode < 200 || $statusCode >= 300) {
            throw new UnexpectedStatusCodeException(
                $statusCode,
                (string) $response->getBody()
            );
        }
    }
}