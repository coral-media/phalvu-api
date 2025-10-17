<?php

namespace App\Tests\Helper;

class JwtParser
{
    public static function getSubject(string $jwt): array
    {
        $parts = explode('.', $jwt);
        if (count($parts) !== 3) {
            throw new \RuntimeException('Invalid token structure');
        }

        $payload = json_decode(base64_decode($parts[1]), true);

        return json_decode($payload['sub'] ?? '{}', true);
    }
}