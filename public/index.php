<?php

declare(strict_types=1);

require_once __DIR__ . "/../vendor/autoload.php";
require_once __DIR__ . "/../src/routes/db.php";
require_once __DIR__ . "/../src/routes/image.php";
require_once __DIR__ . "/../src/routes/user.php";

use Lib\Router;
use Lib\Response;
use Lib\Request;

$app = new Router('public', allow_cors: true);

$app->get('/hello',function(Request $req, Response $res) {

    $res->render('index.html',[],"./");

    // $res->json(['hi' => "hello"],200);
});

$app->get('/upload', function (Request $req, Response $res) {
    $res->render('upload.html', [], "./");
});

$app->get('/gallery', function (Request $req, Response $res) {
    $res->render('gallery.html', [], "./");
});

$app->group($db);
$app->group($image);
$app->group($user);

$app->run();
