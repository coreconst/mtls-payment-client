<?php

namespace Coreconst\MtlsPaymentClient\Http\Factory;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use Coreconst\MtlsPaymentClient\Config\Contracts\ConfigInterface;

class GuzzleClientFactory
{
    public static function create(ConfigInterface $config): ClientInterface
    {
        return new Client([
            'cert'        => [$config->getCertPath(), $config->getKeyPassphrase()],
            'ssl_key'     => [$config->getKeyPath(), $config->getKeyPassphrase()],
            'verify'      => true,
            'http_errors' => false,
        ]);
    }
}