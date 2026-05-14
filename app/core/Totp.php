<?php
/**
 * TOTP (RFC 6238) - Compatible con Google Authenticator, Authy, Microsoft Authenticator.
 * Implementación pura PHP, sin dependencias externas.
 */
class Totp {

    public static function generateSecret(int $length = 16): string {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $out = '';
        for ($i = 0; $i < $length; $i++) $out .= $chars[random_int(0, 31)];
        return $out;
    }

    public static function getCode(string $secret, ?int $timestamp = null): string {
        $key  = self::base32Decode($secret);
        $time = floor(($timestamp ?? time()) / 30);
        $time = pack('N*', 0) . pack('N*', $time);
        $hash = hash_hmac('sha1', $time, $key, true);
        $offset = ord($hash[19]) & 0xf;
        $code = (
            ((ord($hash[$offset    ]) & 0x7f) << 24) |
            ((ord($hash[$offset + 1]) & 0xff) << 16) |
            ((ord($hash[$offset + 2]) & 0xff) <<  8) |
             (ord($hash[$offset + 3]) & 0xff)
        ) % 1000000;
        return str_pad((string)$code, 6, '0', STR_PAD_LEFT);
    }

    public static function verify(string $secret, string $code, int $window = 1): bool {
        $code = preg_replace('/\D/', '', $code);
        if (strlen($code) !== 6) return false;
        $now = time();
        for ($i = -$window; $i <= $window; $i++) {
            if (hash_equals(self::getCode($secret, $now + $i * 30), $code)) return true;
        }
        return false;
    }

    public static function qrUrl(string $secret, string $accountName, string $issuer): string {
        $label = rawurlencode($issuer . ':' . $accountName);
        $params = http_build_query([
            'secret' => $secret,
            'issuer' => $issuer,
            'algorithm' => 'SHA1',
            'digits' => 6,
            'period' => 30,
        ]);
        $otpAuth = "otpauth://totp/{$label}?{$params}";
        return 'https://api.qrserver.com/v1/create-qr-code/?size=220x220&data=' . urlencode($otpAuth);
    }

    private static function base32Decode(string $input): string {
        $map = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $input = strtoupper($input);
        $bin = '';
        for ($i = 0; $i < strlen($input); $i++) {
            $pos = strpos($map, $input[$i]);
            if ($pos === false) continue;
            $bin .= str_pad(decbin($pos), 5, '0', STR_PAD_LEFT);
        }
        $bytes = '';
        for ($i = 0; $i + 8 <= strlen($bin); $i += 8) {
            $bytes .= chr(bindec(substr($bin, $i, 8)));
        }
        return $bytes;
    }
}
