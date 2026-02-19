<?php

namespace Coreconst\MtlsPaymentClient\Config;

use Dotenv\Dotenv;
use Coreconst\MtlsPaymentClient\Config\Contracts\ConfigInterface;

class Config implements ConfigInterface
{
    public function __construct(?string $envPath = null)
    {
        $path = $envPath ?? dirname(__DIR__, 2);

        $dotenv = Dotenv::createImmutable($path);
        $dotenv->load();
        $dotenv->required([
            'MTLS_CERT_PATH',
            'MTLS_KEY_PATH',
            'MTLS_KEY_PASSPHRASE',
            'HMAC_SECRET',
            'PAYMENT_GATEWAY_URL',
        ]);
    }

    public function getCertPath(): string
    {
        return $this->getRequiredString('MTLS_CERT_PATH');
    }

    public function getKeyPath(): string
    {
        return $this->getRequiredString('MTLS_KEY_PATH');
    }

    public function getKeyPassphrase(): string
    {
        return $this->getRequiredString('MTLS_KEY_PASSPHRASE');
    }

    public function getHmacSecret(): string
    {
        return $this->getRequiredString('HMAC_SECRET');
    }

    public function getPaymentGatewayUrl(): string
    {
        return $this->getRequiredString('PAYMENT_GATEWAY_URL');
    }

    private function getRequiredString(string $key): string
    {
        $value = $_ENV[$key] ?? null;

        if ($value === null || $value === '') {
            throw new \RuntimeException("Configuration key '{$key}' is missing or empty");
        }

        return $value;
    }
}