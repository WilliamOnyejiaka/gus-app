<?php

declare(strict_types=1);

namespace Route;

require_once __DIR__ . "/../config/settings.php";
require_once __DIR__ . "/../../vendor/autoload.php";
require_once __DIR__ . "/../config/config.php";
require_once __DIR__ . "/../middlewares/validateBasicAuth.php";

use Lib\Blueprint;
use Lib\Request;
use Lib\Response;
use Controller\UserController;

$user = new Blueprint("/user");
$controller = new UserController();


$user->get('/default-user', "Controller\UserController::defaultUser");
$user->get('/login', "Controller\UserController::login", "login")
    ->middleware(
        $validateBasicAuth("username"),
        "login"
    );
// $user->get('/default-user', [$controller, "defaultUser"]);
