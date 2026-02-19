<?php

namespace Coreconst\MtlsPaymentClient\Tests\Unit;

use Coreconst\MtlsPaymentClient\Config\Contracts\ConfigInterface;
use Coreconst\MtlsPaymentClient\Http\Client\PaymentClient;
use Coreconst\MtlsPaymentClient\Http\Enums\HttpMethod;
use Coreconst\MtlsPaymentClient\Http\Validation\Contracts\ValidatorInterface;
use Coreconst\MtlsPaymentClient\Signature\Contracts\SignerInterface;
use GuzzleHttp\ClientInterface;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

class PaymentClientTest extends TestCase
{
    public function testSendBuildsRequestWithSignatureAndQueryAndValidatesResponse(): void
    {
        $config = $this->createMock(ConfigInterface::class);
        $signer = $this->createMock(SignerInterface::class);
        $client = $this->createMock(ClientInterface::class);
        $validator = $this->createMock(ValidatorInterface::class);
        $response = $this->createMock(ResponseInterface::class);

        $url = 'https://example.com/api/check';
        $payload = [
            'transaction_id' => '12345',
            'amount' => '99.99',
            'currency' => 'USD',
        ];
        $signature = 'test-signature';

        $signer
            ->expects($this->once())
            ->method('sign')
            ->with($payload)
            ->willReturn($signature);

        $client
            ->expects($this->once())
            ->method('request')
            ->with(
                HttpMethod::GET->value,
                $url,
                [
                    'query'   => $payload,
                    'headers' => [
                        'X-Signature' => $signature,
                    ],
                ]
            )
            ->willReturn($response);

        $validator
            ->expects($this->once())
            ->method('validate')
            ->with($response);

        $paymentClient = new PaymentClient($config, $signer, $client, $validator);

        $result = $paymentClient->send($url, $payload, HttpMethod::GET);

        $this->assertSame($response, $result);
    }

    public function testSendToGatewayUsesConfigUrl(): void
    {
        $config = $this->createMock(ConfigInterface::class);
        $signer = $this->createMock(SignerInterface::class);
        $client = $this->createMock(ClientInterface::class);
        $validator = $this->createMock(ValidatorInterface::class);
        $response = $this->createMock(ResponseInterface::class);

        $gatewayUrl = 'https://gateway.test/api/check';
        $payload = [
            'transaction_id' => '12345',
            'amount' => '99.99',
            'currency' => 'USD',
        ];
        $signature = 'gateway-signature';

        $config
            ->expects($this->once())
            ->method('getPaymentGatewayUrl')
            ->willReturn($gatewayUrl);

        $signer
            ->expects($this->once())
            ->method('sign')
            ->with($payload)
            ->willReturn($signature);

        $client
            ->expects($this->once())
            ->method('request')
            ->with(
                HttpMethod::GET->value,
                $gatewayUrl,
                [
                    'query'   => $payload,
                    'headers' => [
                        'X-Signature' => $signature,
                    ],
                ]
            )
            ->willReturn($response);

        $validator
            ->expects($this->once())
            ->method('validate')
            ->with($response);

        $paymentClient = new PaymentClient($config, $signer, $client, $validator);

        $result = $paymentClient->sendToGateway($payload, HttpMethod::GET);

        $this->assertSame($response, $result);
    }
}

