<?php

namespace Coreconst\MtlsPaymentClient\Factory;

use Coreconst\MtlsPaymentClient\Http\Client\PaymentClient;
use Coreconst\MtlsPaymentClient\Config\Config;
use Coreconst\MtlsPaymentClient\Signature\HmacSigner;

class PaymentClientFactory
{
    public static function create(?string $envPath = null): PaymentClient
    {
        $config = new Config($envPath);
        $signer = new HmacSigner($config->getHmacSecret());
        return new PaymentClient($config, $signer);
    }
}