<?php

declare(strict_types=1);

require_once __DIR__ . "/../config/settings.php";
require_once __DIR__ . "/../../vendor/autoload.php";

use Lib\Request;
use Lib\Response;
use Service\JWTService;

$authorization = function (array $audience) {

    return function (Request $request, Response $response) use ($audience) {
        $token = $request->get_header('Authorization');

        if (!$token) {
            $response->json([
                "error" => true,
                'message' => "Authorization header missing"
            ], 401);
        } else {
            $jwt = JWTService::getJWT($token);
            if (!$jwt) {
                $response->json([
                    'error' => true,
                    'message' => "Invalid token"
                ], 400);
            } else {
                $jwtData = JWTService::jwtData($jwt, $audience);
                if(empty($jwtData['payload'])){
                    $response->json([
                        'error' => true,
                        'message' => $jwtData['message']
                    ], 400);
                }

                $payload = $jwtData['payload'];
                
                if(in_array($payload->{'aud'}, $audience)){
                    $response->json([
                        'error' => true,
                        'message' =>"Unauthorized user"
                    ], 401);
                }
                $request->locals("data", $payload->data);
            }
        }
    };
};
