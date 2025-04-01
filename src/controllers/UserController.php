<?php

declare(strict_types=1);

namespace Controller;

require_once __DIR__ . "/../config/settings.php";
require_once __DIR__ . "/../../vendor/autoload.php";
require_once __DIR__ . "/../config/config.php";


use Lib\Request;
use Lib\Response;
use Service\UserService;

class UserController
{
    public UserService $service;

    public function __construct()
    {
        $this->service = new UserService();
    }

    public function defaultUser(Request $req, Response $res)
    {
        $username = config('username');
        $password = config('password');
        $result = $this->service->createDefaultUser($username,$password);
        $res->json($result['json'], $result['statusCode']);
    }

    public function login(Request $req,Response $res){
        $username = $req->authorization("email");
        $password = $req->authorization("password");
        $result = $this->service->login($username,$password);
        $res->json($result['json'],$result['statusCode']);
    }
}
