<?php

namespace Coreconst\MtlsPaymentClient\Config\Contracts;

interface ConfigInterface
{
    public function getCertPath(): string;
    public function getKeyPath(): string;
    public function getKeyPassphrase(): string;
    public function getHmacSecret(): string;
    public function getPaymentGatewayUrl(): string;
}