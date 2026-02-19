<?php

namespace Coreconst\MtlsPaymentClient\Signature;

use Coreconst\MtlsPaymentClient\Signature\Contracts\SignerInterface;

class HmacSigner implements SignerInterface
{
    public function __construct(private readonly string $secret)
    {
    }

    public function sign(array $payload): string
    {
        $data = $this->normalizePayload($payload);
        return hash_hmac('sha256', $data, $this->secret);
    }

    private function normalizePayload(array $payload): string
    {
        ksort($payload);
        return http_build_query($payload);
    }
}