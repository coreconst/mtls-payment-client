<?php

namespace Coreconst\MtlsPaymentClient\Tests\Integration;

use Coreconst\MtlsPaymentClient\Config\Config;
use Coreconst\MtlsPaymentClient\Factory\PaymentClientFactory;
use Coreconst\MtlsPaymentClient\Http\Enums\HttpMethod;
use PHPUnit\Framework\TestCase;

class PaymentClientIntegrationTest extends TestCase
{
    public function testSendRealRequestWithMtlsAndHmac(): void
    {
        $projectRoot = dirname(__DIR__, 2);
        $envFile = $projectRoot . '/.env';

        if (!file_exists($envFile)) {
            $this->markTestSkipped('.env file is not configured for integration test');
        }

        try {
            $config = new Config($projectRoot);
        } catch (\Throwable $e) {
            $this->markTestSkipped('Integration environment is not fully configured: ' . $e->getMessage());
        }

        $certPath = $config->getCertPath();
        $keyPath = $config->getKeyPath();

        if (!file_exists($certPath) || !file_exists($keyPath)) {
            $this->markTestSkipped('mTLS certificate or key file does not exist');
        }

        $client = PaymentClientFactory::create($projectRoot);

        $payload = [
            'transaction_id' => '12345',
            'amount'         => '99.99',
            'currency'       => 'USD',
        ];

        $response = $client->sendToGateway($payload, HttpMethod::GET);

        $statusCode = $response->getStatusCode();

        $this->assertGreaterThanOrEqual(200, $statusCode);
        $this->assertLessThan(300, $statusCode);
        $this->assertNotSame('', (string) $response->getBody());
    }
}

