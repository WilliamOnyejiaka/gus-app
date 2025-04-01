<?php

declare(strict_types=1);

namespace Service;

require_once __DIR__ . "/../config/settings.php";
require_once __DIR__ . "/../../vendor/autoload.php";
require_once __DIR__ . "/../config/config.php";

use Exception;
use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;

class JWTService
{

    public static function getJWT(string $token)
    {
        $check_token = preg_match('/Bearer\s(\S+)/', $token, $matches);
        return $check_token == 0 ? false : $matches[1];
    }

    public static function generateToken(array $data, array $audience, int $expirationTime = 2592000): string
    {
        $iat = time();
        $nbf = $iat;
        $exp = $iat + $expirationTime;
        $aud = $audience;

        $payload = array(
            'iat' => $iat,
            'nbf' => $nbf,
            'exp' => $exp,
            'aud' => $aud,
            'data' => $data
        );
        return JWT::encode(
            $payload,
            config("secretKey"),
            config("hash")
        );
    }

    public static function jwtData(string $jwt)
    {

        try {
            $payload = (JWT::decode($jwt, new Key(config("secretKey"), config("hash"))));
            return [
                'message' => null,
                'payload' => $payload
            ];
        } catch (\Firebase\JWT\ExpiredException $ex) {
            return [
                'message' => $ex->getMessage(),
                'payload' => []
            ];
        } catch (Exception $ex) {
            return [
                'message' => $ex->getMessage(),
                'payload' => []
            ];
        }
    }
}
