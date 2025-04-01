<?php

declare(strict_types=1);

require_once __DIR__ . "/../config/settings.php";
require_once __DIR__ . "/../../vendor/autoload.php";

use Lib\Request;
use Lib\Response;

$validateBody = function ($neededValues) {

    return function (Request $request, Response $response) use ($neededValues) {
        if (count($neededValues) == 1 && empty($request->body($neededValues[0]))) {
            $response->json([
                'error' => true,
                'message' => "$neededValues[0] needed"
            ], 400);
        } else {
            foreach ($neededValues as $value) {
                if (empty($request->body($value))) {
                    $response->json([
                        'error' => true,
                        'message' => "all values needed"
                    ], 400);
                }
            }
        }
    };
};