<?php

namespace App\Modules\Auth;

class JwtService
{
    private static string $algo = 'HS256';

    private static function getSecretKey(): string
    {
        $key = env('JWT_SECRET');

        if (empty($key)) {
            throw new \Exception('JWT secret key not found in environment variables.');
        }

        return $key;
    }

    public static function generateToken(array $payload, int $expiryInSeconds = 3600): string
    {
        $issuedAt = time();
        $expireAt = $issuedAt + $expiryInSeconds;

        $payload['iat'] = $issuedAt;
        $payload['exp'] = $expireAt;

        $base64Header = self::base64UrlEncode(
            json_encode([
                'alg' => self::$algo,
                'typ' => 'JWT',
            ])
        );

        $base64Payload = self::base64UrlEncode(
            json_encode($payload)
        );

        $secretKey = self::getSecretKey();

        $signature = hash_hmac(
            'sha256',
            "$base64Header.$base64Payload",
            $secretKey,
            true
        );

        $base64Signature = self::base64UrlEncode($signature);

        return "$base64Header.$base64Payload.$base64Signature";
    }

    public static function decodeToken(string $token): ?array
    {
        $parts = explode('.', $token);

        if (count($parts) !== 3) {
            return null;
        }

        [$header, $payload, $signature] = $parts;

        $secretKey = self::getSecretKey();

        $expectedSignature = self::base64UrlEncode(
            hash_hmac(
                'sha256',
                "$header.$payload",
                $secretKey,
                true
            )
        );

        if (! hash_equals($expectedSignature, $signature)) {
            return null;
        }

        $decodedPayload = json_decode(
            self::base64UrlDecode($payload),
            true
        );

        if (! is_array($decodedPayload)) {
            return null;
        }

        if (
            isset($decodedPayload['exp']) &&
            time() > $decodedPayload['exp']
        ) {
            return null;
        }

        return $decodedPayload;
    }

    private static function base64UrlEncode(string $data): string
    {
        return rtrim(
            strtr(base64_encode($data), '+/', '-_'),
            '='
        );
    }

    private static function base64UrlDecode(string $data): string
    {
        $remainder = strlen($data) % 4;

        if ($remainder) {
            $data .= str_repeat('=', 4 - $remainder);
        }

        return base64_decode(
            strtr($data, '-_', '+/')
        );
    }
}
