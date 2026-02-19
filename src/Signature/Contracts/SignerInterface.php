<?php

namespace Coreconst\MtlsPaymentClient\Signature\Contracts;

interface SignerInterface
{
    public function sign(array $payload): string;
}