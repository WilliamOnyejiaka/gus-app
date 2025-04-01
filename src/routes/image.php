<?php

declare(strict_types=1);

namespace Route;

require_once __DIR__ . "/../config/settings.php";
require_once __DIR__ . "/../../vendor/autoload.php";
require_once __DIR__ . "/../config/config.php";
require_once __DIR__ . "/../middlewares/authorization.php";

use Lib\Blueprint;
use Lib\Request;
use Lib\Response;
use Controller\ImageController;

$image = new Blueprint("/image");
$controller = new ImageController();

$image->use($authorization(['users']));

$image->post('/upload', [$controller, "create"]);
$image->get("/{fileName}",[$controller,"read"]);
$image->get("",[$controller,"pagination"]);
$image->get("/search/{keyword}",[$controller, "searchPagination"]);
$image->patch("/name",[$controller,"updateName"]);
$image->patch("/description",[$controller, "updateDescription"]);
$image->patch("",[$controller, "update"]);
$image->delete("/{id}",[$controller, "delete"]);