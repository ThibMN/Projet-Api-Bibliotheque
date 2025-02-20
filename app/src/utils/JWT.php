<?php

namespace App\Utils;

class JWT {
    private static $secret;

    private static function getSecret() {
        if (self::$secret === null) {
            self::$secret = getenv('JWT_AUTH_SECRET') ?: 'mon-super-secret-de-dev';
            
            if (self::$secret === false) {
                throw new \RuntimeException('JWT_AUTH_SECRET environment variable is not set');
            }
        }
        return self::$secret;
    }

    public static function generate($payload) {
        // Base 64
        // Header
        $header = self::base64UrlEncode(json_encode(['alg' => 'HS256', 'typ' => 'JWT']));
        // Payload
        $payload = self::base64UrlEncode(json_encode($payload));
        
        // Concaténation header . payload
        $concat_signature = "$header.$payload";
        // Génération de la signature avec hash
        $signature = hash_hmac("sha256", $concat_signature, self::getSecret(), true);
        // base64 de la signature
        $signature = self::base64UrlEncode($signature);

        // Token complet
        return "$concat_signature.$signature";
    }
    public static function decode($jwt) {
        $parts = explode('.', $jwt);
        if (count($parts) !== 3) {
            throw new \InvalidArgumentException('Invalid JWT');
        }

        list($header, $payload, $signature) = $parts;

        // Vérifier la signature
        $valid_signature = hash_hmac('sha256', "$header.$payload", self::getSecret(), true);
        $valid_signature = self::base64UrlEncode($valid_signature);

        if (!hash_equals($valid_signature, $signature)) {
            throw new \UnexpectedValueException('Invalid signature');
        }

        // Décoder le payload
        $decoded_payload = json_decode(self::base64UrlDecode($payload), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \UnexpectedValueException('Invalid payload');
        }

        return $decoded_payload;
    }

    public static function verify($jwt) {
        // Ensure the JWT has the correct number of segments
        $segments = explode('.', $jwt);
        if (count($segments) !== 3) {
            return false;  // Invalid JWT structure
        }

        list($header, $payload, $signature) = $segments;
        $expectedSignature = self::base64UrlEncode(
            hash_hmac('sha256', "$header.$payload", self::getSecret(), true)
        );
        
        return hash_equals($expectedSignature, $signature);
    }
  
    private static function base64UrlEncode($data) {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), characters: '=');
    }

    private static function base64UrlDecode($data) {
        $urlUnsafeData = str_replace(['-', '_'], ['+', '/'], $data);
        return base64_decode($urlUnsafeData);
    }
}