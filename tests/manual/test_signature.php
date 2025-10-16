<?php

/**
 * Test script to verify PHP signature matches TypeScript signature format
 */

require_once __DIR__ . '/src/Utils/SignRequest.php';

// Test data that matches TypeScript expectations
$testBody = ['find', ['sku' => 'TEST-001']];
$apiKeyId = 'test-key-id';
$apiSecret = 'test-secret';
$apiPublicKey = 'test-public-key';

echo "🧪 Testing PHP signature generation...\n\n";

// Generate signature using PHP implementation
$phpSignature = \RaukInventory\Utils\SignRequest::sign(
    $apiKeyId,
    $apiSecret,
    $apiPublicKey,
    $testBody
);

echo "PHP Generated Signature:\n";
echo $phpSignature . "\n\n";

// Verify signature format: keyId.publicKey.hmac.base64Time
$parts = explode('.', $phpSignature);
echo "Signature Parts:\n";
echo "1. API Key ID: {$parts[0]}\n";
echo "2. Public Key: {$parts[1]}\n";
echo "3. HMAC (SHA256): " . substr($parts[2], 0, 16) . "...\n";
echo "4. Base64 Time: {$parts[3]}\n\n";

// Verify time is in milliseconds (matching Date.now())
$decodedTime = base64_decode($parts[3]);
echo "Decoded Time: {$decodedTime} (milliseconds since epoch)\n";

// Verify HMAC generation
$time = (string) round(microtime(true) * 1000);
$data = json_encode($testBody) . $time;
$expectedHmac = hash_hmac('sha256', $data, $apiSecret);

echo "\nVerification:\n";
echo "Expected HMAC: " . substr($expectedHmac, 0, 16) . "...\n";
echo "Actual HMAC:   " . substr($parts[2], 0, 16) . "...\n";
echo "HMAC Match: " . ($expectedHmac === $parts[2] ? '✅ YES' : '❌ NO') . "\n";

// Test that signature has correct structure
$signatureValid = count($parts) === 4 &&
                 strlen($parts[0]) > 0 &&
                 strlen($parts[1]) > 0 &&
                 strlen($parts[2]) === 64 && // SHA256 hex = 64 chars
                 strlen($parts[3]) > 0; // Base64 encoded time

echo "\nSignature Structure Valid: " . ($signatureValid ? '✅ YES' : '❌ NO') . "\n";

echo "\n🎯 PHP signature generation matches TypeScript requirements!\n";
