<?php

namespace App\Helpers;

class Encryptor
{
    private static $cipher = 'aes-256-cbc';

    public static function encrypt($data, $secretKey)
    {
        $key = substr(hash('sha256', $secretKey, true), 0, 32);

        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length(self::$cipher));

        $encrypted = openssl_encrypt($data, self::$cipher, $key, 0, $iv);

        return base64_encode($encrypted . '::' . base64_encode($iv));
    }

    public static function decrypt($data, $secretKey)
    {
        $key = substr(hash('sha256', $secretKey, true), 0, 32);

        list($encryptedData, $iv) = explode('::', base64_decode($data), 2);

        return openssl_decrypt($encryptedData, self::$cipher, $key, 0, base64_decode($iv));
    }
}
