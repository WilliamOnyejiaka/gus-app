<?php

declare(strict_types=1);

require_once __DIR__ . "/../config/settings.php";
require_once __DIR__ . "/../../vendor/autoload.php";

use Lib\Request;
use Lib\Response;

$validateBasicAuth = function (string $name) {
    return function (Request $request, Response $response) use ($name) {

        $email = $request->authorization("email");
        $password = $request->authorization("password");

        if (!($email && $password)) {
            $response->json([
                'error' => true,
                'message' => "$name and password required"
            ], 400);
        }
    };
};
