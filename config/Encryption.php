<?php
namespace Config;

class Encryption {
    // Clave secreta (en un entorno real debe ir en variables de entorno o archivo .env)
    private static $key = 'MarketPrimaveraSecretKey12345678'; 
    private static $method = 'aes-256-cbc';

    public static function encrypt($data) {
        $ivSize = openssl_cipher_iv_length(self::$method);
        $iv = openssl_random_pseudo_bytes($ivSize);
        $encrypted = openssl_encrypt($data, self::$method, self::$key, 0, $iv);
        return base64_encode($iv . $encrypted);
    }

    public static function decrypt($data) {
        $data = base64_decode($data);
        $ivSize = openssl_cipher_iv_length(self::$method);
        $iv = substr($data, 0, $ivSize);
        $encrypted = substr($data, $ivSize);
        return openssl_decrypt($encrypted, self::$method, self::$key, 0, $iv);
    }
}
