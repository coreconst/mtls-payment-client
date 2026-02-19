<?php

namespace Coreconst\MtlsPaymentClient\Http\Client;

use GuzzleHttp\ClientInterface;
use Psr\Http\Message\ResponseInterface;
use Coreconst\MtlsPaymentClient\Config\Contracts\ConfigInterface;
use Coreconst\MtlsPaymentClient\Signature\Contracts\SignerInterface;
use Coreconst\MtlsPaymentClient\Http\Enums\HttpMethod;
use Coreconst\MtlsPaymentClient\Http\Factory\GuzzleClientFactory;
use Coreconst\MtlsPaymentClient\Http\Validation\Contracts\ValidatorInterface;
use Coreconst\MtlsPaymentClient\Http\Validation\ResponseValidator;

class PaymentClient
{
    private ClientInterface $client;

    public function __construct(
        private readonly ConfigInterface $config,
        private readonly SignerInterface $signer,
        ?ClientInterface $httpClient = null,
        private readonly ValidatorInterface $validator = new ResponseValidator()
    ) {
        $this->client = $httpClient ?? GuzzleClientFactory::create($config);
    }

    public function send(string $url, array $payload, HttpMethod $method = HttpMethod::GET): ResponseInterface
    {
        $signature = $this->signer->sign($payload);

        $response = $this->client->request($method->value, $url, [
            'query'   => $payload,
            'headers' => [
                'X-Signature' => $signature,
            ],
        ]);

        $this->validator->validate($response);

        return $response;
    }
    
    public function sendToGateway(array $payload, HttpMethod $method = HttpMethod::GET): ResponseInterface
    {
        return $this->send($this->config->getPaymentGatewayUrl(), $payload, $method);
    }
}