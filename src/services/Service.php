<?php

declare(strict_types=1);

namespace Service;

require_once __DIR__ . "/../config/settings.php";
require_once __DIR__ . "/../../vendor/autoload.php";


class Service
{

    public function __construct()
    {
    }

    public function responseData(int $statusCode,bool $error,string $message, mixed $data = null){
        return [
            'statusCode' => $statusCode,
            'json' => [
                'error' => $error,
                'message' => $message,
                'data' => $data
            ],
        ];
    }

}
