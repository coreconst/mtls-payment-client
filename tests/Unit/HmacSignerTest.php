<?php

namespace Coreconst\MtlsPaymentClient\Tests\Unit;

use Coreconst\MtlsPaymentClient\Signature\HmacSigner;
use PHPUnit\Framework\TestCase;

class HmacSignerTest extends TestCase
{
    private const SECRET = 'test-secret-key-12345';

    public function testSignWithSimplePayload(): void
    {
        $signer = new HmacSigner(self::SECRET);
        $payload = ['amount' => '100', 'currency' => 'USD'];

        $signature = $signer->sign($payload);

        $this->assertIsString($signature);
        $this->assertEquals(64, strlen($signature)); // SHA256 produces 64 symbols
        $this->assertMatchesRegularExpression('/^[a-f0-9]{64}$/', $signature); // hex string 
    }

    public function testSignProducesSameSignatureForSamePayload(): void
    {
        $signer = new HmacSigner(self::SECRET);
        $payload = ['amount' => '100', 'currency' => 'USD'];

        $signature1 = $signer->sign($payload);
        $signature2 = $signer->sign($payload);

        $this->assertEquals($signature1, $signature2);
    }

    public function testSignProducesDifferentSignatureForDifferentSecrets(): void
    {
        $signer1 = new HmacSigner('secret1');
        $signer2 = new HmacSigner('secret2');
        $payload = ['amount' => '100', 'currency' => 'USD'];

        $signature1 = $signer1->sign($payload);
        $signature2 = $signer2->sign($payload);

        $this->assertNotEquals($signature1, $signature2);
    }

    public function testSignProducesDifferentSignatureForDifferentPayloads(): void
    {
        $signer = new HmacSigner(self::SECRET);
        $payload1 = ['amount' => '100', 'currency' => 'USD'];
        $payload2 = ['amount' => '200', 'currency' => 'USD'];

        $signature1 = $signer->sign($payload1);
        $signature2 = $signer->sign($payload2);

        $this->assertNotEquals($signature1, $signature2);
    }

    public function testPayloadNormalizationSortsKeys(): void
    {
        $signer = new HmacSigner(self::SECRET);
        
        $payload1 = ['z' => 'last', 'a' => 'first', 'm' => 'middle'];
        $payload2 = ['a' => 'first', 'm' => 'middle', 'z' => 'last'];

        $signature1 = $signer->sign($payload1);
        $signature2 = $signer->sign($payload2);

        $this->assertEquals($signature1, $signature2);
    }

    public function testSignWithEmptyPayload(): void
    {
        $signer = new HmacSigner(self::SECRET);
        $payload = [];

        $signature = $signer->sign($payload);

        $this->assertIsString($signature);
        $this->assertEquals(64, strlen($signature));
    }

    public function testSignWithNumericValues(): void
    {
        $signer = new HmacSigner(self::SECRET);
        $payload = ['amount' => 100, 'quantity' => 5];

        $signature = $signer->sign($payload);

        $this->assertIsString($signature);
        $this->assertEquals(64, strlen($signature));
    }

    public function testSignWithSpecialCharacters(): void
    {
        $signer = new HmacSigner(self::SECRET);
        $payload = [
            'description' => 'Test & Payment',
            'email' => 'user@example.com',
            'data' => 'value=with=equals&and=ampersands'
        ];

        $signature = $signer->sign($payload);

        $this->assertIsString($signature);
        $this->assertEquals(64, strlen($signature));
    }

    public function testSignWithUnicodeCharacters(): void
    {
        $signer = new HmacSigner(self::SECRET);
        $payload = ['name' => 'Тест', 'description' => 'Оплата'];

        $signature = $signer->sign($payload);

        $this->assertIsString($signature);
        $this->assertEquals(64, strlen($signature));
    }

    public function testSignWithBooleanValues(): void
    {
        $signer = new HmacSigner(self::SECRET);
        $payload = ['active' => true, 'verified' => false];

        $signature = $signer->sign($payload);

        $this->assertIsString($signature);
        $this->assertEquals(64, strlen($signature));
    }

    public function testSignWithNullValues(): void
    {
        $signer = new HmacSigner(self::SECRET);
        $payload = ['optional' => null, 'required' => 'value'];

        $signature = $signer->sign($payload);

        $this->assertIsString($signature);
        $this->assertEquals(64, strlen($signature));
    }

    public function testSignWithLargePayload(): void
    {
        $signer = new HmacSigner(self::SECRET);
        $payload = [];
        
        for ($i = 0; $i < 100; $i++) {
            $payload["key_$i"] = "value_$i";
        }

        $signature = $signer->sign($payload);

        $this->assertIsString($signature);
        $this->assertEquals(64, strlen($signature));
    }

    public function testSignVerificationWithExpectedHash(): void
    {
        $signer = new HmacSigner('my-secret-key');
        $payload = ['amount' => '100', 'currency' => 'USD'];
        
        $signature = $signer->sign($payload);
        
        // Verify using PHP's hash_hmac directly
        ksort($payload);
        $expected = hash_hmac('sha256', http_build_query($payload), 'my-secret-key');
        
        $this->assertEquals($expected, $signature);
    }

    public function testSignWithComplexNestedStructure(): void
    {
        $signer = new HmacSigner(self::SECRET);
        // Note: nested arrays will be converted to string representation by http_build_query
        $payload = [
            'order_id' => '12345',
            'items' => ['item1', 'item2'], // This will be converted to string
            'metadata' => ['key' => 'value'] // This will be converted to string
        ];

        $signature = $signer->sign($payload);

        $this->assertIsString($signature);
        $this->assertEquals(64, strlen($signature));
    }
}
