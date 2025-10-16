<?php

namespace RaukInventory\Utils;

/**
 * Signs a request to the Rauk Inventory API
 */
class SignRequest
{
    /**
     * Sign a request with HMAC-SHA256
     *
     * @param string $apiKeyId The API key ID
     * @param string $apiSecret The API secret
     * @param string $apiPublicKey The API public key
     * @param array $body The request body
     * @return string The signed request signature
     * @throws \Exception If signature generation fails
     */
    public static function sign(
        string $apiKeyId,
        string $apiSecret,
        string $apiPublicKey,
        array $body
    ): string {
        $time = (string) round(microtime(true) * 1000); // milliseconds since epoch (matching Date.now())
        $data = json_encode($body) . $time;

        try {
            $hmac = hash_hmac('sha256', $data, $apiSecret);
            $b64Time = base64_encode($time);

            return "{$apiKeyId}.{$apiPublicKey}.{$hmac}.{$b64Time}";
        } catch (\Exception $e) {
            throw new \Exception("Failed to generate signature: " . $e->getMessage());
        }
    }
}
