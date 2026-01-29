<?php
namespace Core;

class Auth
{
    public static function issueToken($userId)
    {
        $header = array('alg' => 'HS256', 'typ' => 'JWT');
        $payload = array(
            'sub' => $userId,
            'iat' => time(),
            'exp' => time() + config('jwt.ttl'),
        );
        $segments = array(
            self::base64UrlEncode(json_encode($header)),
            self::base64UrlEncode(json_encode($payload)),
        );
        $signature = hash_hmac('sha256', implode('.', $segments), config('jwt.secret'), true);
        $segments[] = self::base64UrlEncode($signature);
        return implode('.', $segments);
    }

    public static function verify($token)
    {
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return false;
        }
        list($header64, $payload64, $sig64) = $parts;
        $signature = self::base64UrlEncode(hash_hmac('sha256', $header64 . '.' . $payload64, config('jwt.secret'), true));
        if (!hash_equals($signature, $sig64)) {
            return false;
        }
        $payload = json_decode(self::base64UrlDecode($payload64), true);
        if (!$payload || (isset($payload['exp']) && $payload['exp'] < time())) {
            return false;
        }
        return $payload;
    }

    private static function base64UrlEncode($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private static function base64UrlDecode($data)
    {
        $remainder = strlen($data) % 4;
        if ($remainder) {
            $data .= str_repeat('=', 4 - $remainder);
        }
        return base64_decode(strtr($data, '-_', '+/'));
    }
}
