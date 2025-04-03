<?php

declare(strict_types=1);

namespace Controller;

require_once __DIR__ . "/../config/settings.php";
require_once __DIR__ . "/../../vendor/autoload.php";


use Lib\Request;
use Lib\Response;
use Service\ImageService;

class ImageController
{

    public ImageService $service;

    public function __construct()
    {
        $this->service = new ImageService();
    }

    public function create(Request $req, Response $res)
    {

        function something($url){
            if (file_exists($url)) {
                list($width, $height) = getimagesize($url);
                $aspectRatio = $width / $height;

                // Define thresholds
                $isWide = $aspectRatio > 1.5;       // Wider than 3:2
                $isTall = $aspectRatio < 0.67;      // Taller than 2:3
                $isBig = $width >= 600 && $height >= 600; // Large in both dimensions
                $isNormal = !$isWide && !$isTall && !$isBig; // Neither wide, tall, nor big


                // Determine the class
                if ($isBig) return "big";
                if ($isWide) return 'wide';
                if ($isTall) return 'tall';
                if($isNormal) return 'normal';
            }
            return null;
        }
        $name = (string) $req->body('name');
        $description = (string) $req->body('description');
        $image = $req->file('image');

        if (!$image) {
            $res->json([
                'error' => true,
                'message' => "image is required"
            ], 400);
        }

        $result = $this->service->create($name, $description, $image);
        $res->json([
            'data' => $result['json'],
            'testing' => something("./..".$result['json']['data']['url'])
        ], $result['statusCode']);
    }

    public function read(Request $req, Response $res)
    {
        $fileName = $req->param('fileName');
        $result = $this->service->readWithFileName($fileName);
        $res->json($result['json'], $result['statusCode']);
    }

    public function updateDescription(Request $req, Response $res)
    {
        $description = $req->body('description');
        $id = (int)$req->body('id');
        $result = $this->service->updateDescription($description, $id);
        $res->json($result['json'], $result['statusCode']);
    }

    public function updateName(Request $req, Response $res)
    {
        $name = $req->body('name');
        $id = (int)$req->body('id');
        $result = $this->service->updateName($name, $id);
        $res->json($result['json'], $result['statusCode']);
    }

    public function update(Request $req, Response $res)
    {
        $name = $req->body('name');
        $description = $req->body('description');
        $id = (int)$req->body('id');
        $result = $this->service->update($name, $description, $id);
        $res->json($result['json'], $result['statusCode']);
    }

    public function delete(Request $req, Response $res)
    {
        $id = (int)$req->param('id');
        $result = $this->service->delete($id);
        $res->json($result['json'], $result['statusCode']);
    }

    public function pagination(Request $req, Response $res)
    {
        $page = (int)$req->args('page', 1);
        $limit = (int)$req->args('limit',10);
        $result = $this->service->getPagination($page, $limit);
        $res->json($result['json'], $result['statusCode']);
    }

    public function searchPagination(Request $req, Response $res)
    {
        $page = (int)$req->args('page', 1);
        $limit = (int)$req->args('limit', 10);
        $keyword = $req->param('keyword');
        $result = $this->service->searchPagination($keyword, $page, $limit);
        $res->json($result['json'], $result['statusCode']);
    }
}
