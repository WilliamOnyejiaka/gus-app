<?php

declare(strict_types=1);

namespace Route;

require_once __DIR__ . "/../config/settings.php";
require_once __DIR__ . "/../../vendor/autoload.php";
require_once __DIR__ . "/../config/config.php";


use Lib\Blueprint;
use Lib\Request;
use Lib\Response;
use Service\DBManger;
use Controller\DatabaseController;

$db = new Blueprint("/db");
$controller = new DatabaseController();


$db->get('/migrate', [$controller, "migrate"]);
$db->get('/drop', [$controller, "drop"]);
