<?php

declare(strict_types=1);

namespace Controller;

require_once __DIR__ . "/../config/settings.php";
require_once __DIR__ . "/../../vendor/autoload.php";


use Lib\Request;
use Lib\Response;
use Service\DBManger;

class DatabaseController
{

    public static function migrate(Request $request, Response $response){
        $successfulMigration = DBManger::migrate();
        if ($successfulMigration) {
            $response->json([
                'error' => true,
                'message' => "something went wrong"
            ], 500);
        }

        $response->json([
            'error' => false,
            'message' => "database was created successfully"
        ], 200);
    }

    public static function drop(Request $request, Response $response)
    {
        $successfulDrop = DBManger::drop();

        if ($successfulDrop) {
            $response->json([
                'error' => true,
                'message' => "something went wrong"
            ], 500);
        }

        $response->json([
            'error' => false,
            'message' => "database was dropped successfully"
        ], 200);
    }
}
