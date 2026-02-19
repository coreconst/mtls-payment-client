<?php

namespace Coreconst\MtlsPaymentClient\Http\Validation\Contracts;

use Psr\Http\Message\ResponseInterface;

interface ValidatorInterface
{
    public function validate(ResponseInterface $response): void;
}