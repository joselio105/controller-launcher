<?php

namespace Plugse\Ctrl\helpers;

use DateInterval;
use DateTime;
use Plugse\Ctrl\errors\TokenDecodeError;
use Plugse\Ctrl\http\Request;
use Plugse\Fp\Env;

class Crypto
{
    private const BASE64_PADDING_LENGTH = 4;
    private const SECRET_KEY_FILE = './config/crypto.env';
    private const TOKEN_EXPIRATION = 'PT1H';

    public static function hash($value)
    {
        return hash('gost-crypto', $value);
    }

    public static function CreateJWT(int $id, int $expiration = null)
    {
        $expiration = is_null($expiration) ? self::getExpirationTime() : $expiration;

        $header = [
            'typ' => 'JWT',
            'alg' => 'HS256',
        ];
        $payload = [
            'jti' => self::getJti(),
            'exp' => $expiration,
            'uid' => $id,
        ];
        $secretKey = self::getSecret();

        $jsonHeader = json_encode($header);
        $jsonPayload = json_encode($payload);
        $base64Header = self::base64UrlEncode($jsonHeader);
        $base64Payload = self::base64UrlEncode($jsonPayload);

        $sign = hash_hmac('sha256', "{$base64Header}.{$base64Payload}", $secretKey, true);
        $base64Sign = self::base64UrlEncode($sign);

        return [
            'token' => "{$base64Header}.{$base64Payload}.{$base64Sign}",
            'expiration' => $expiration,
        ];
    }

    public static function GetTokenOnHeader(Request $request): string
    {
        preg_match('/Bearer\s(\S+)/', $request->header['Authorization'], $matches);

        return $matches[1];
    }

    public static function DecodeToken(string $token): array
    {
        $tokenHeader = explode('.', $token)[1];
        $tokenBase64 = self::base64UrlDecode($tokenHeader);
        $tokenArray = (array) json_decode($tokenBase64, JSON_PRETTY_PRINT);

        /* if(!key_exists('exp', $tokenArray)){
            var_dump($tokenArray);die;
            throw new Exception('Erro: Token inválido');
        } */

        return $tokenArray;
    }

    public static function getTimestamp($date = 'now')
    {
        $dateObject = new DateTime($date);

        return $dateObject->getTimestamp() * 1000;
    }

    private static function getExpirationTime()
    {
        $date = new DateTime();

        $date->add(new DateInterval(self::TOKEN_EXPIRATION));

        return $date->getTimestamp() * 1000;
    }

    private static function getJti()
    {
        return md5(uniqid(rand()));
    }

    private static function getSecret()
    {
        if (file_exists(self::SECRET_KEY_FILE)) {
            $key = Env::read(self::SECRET_KEY_FILE);

            return $key['secret'];
        } else {
            return self::generateSecret();
        }
    }

    private static function generateSecret()
    {
        $key = $_SERVER['SCRIPT_FILENAME'] . self::getJti();
        $key = self::hash($key);
        Env::save(self::SECRET_KEY_FILE, ['secret' => $key]);

        return $key;
    }

    public static function base64UrlEncode(string $data): string
    {
        return str_replace('=', '', strtr(base64_encode($data), '+/', '-_'));
    }

    public static function base64UrlDecode(string $data): string
    {
        $remainder = strlen($data) % self::BASE64_PADDING_LENGTH;

        if ($remainder !== 0) {
            $data .= str_repeat('=', self::BASE64_PADDING_LENGTH - $remainder);
        }

        $decodedContent = base64_decode(strtr($data, '-_', '+/'), true);

        if (!is_string($decodedContent)) {
            throw new TokenDecodeError();
        }

        return $decodedContent;
    }
}
