<?php

declare(strict_types=1);

namespace Service;

require_once __DIR__ . "/../config/settings.php";
require_once __DIR__ . "/../../vendor/autoload.php";

use Repo\UserRepo;
use Util\Password;
use Service\Service;
use Service\JWTService;

class UserService extends Service
{

    public UserRepo $repo;

    public function __construct()
    {
        parent::__construct();
        $this->repo =  new UserRepo();
    }

    public function createDefaultUser(string $username, string $password)
    {
        $user = $this->repo->getWithUsername($username);
        if ($user) return $this->responseData(400, true, "User already exists");

        $created =  $this->repo->create([
            'username' => $username,
            'password' => Password::hash($password)
        ]);

        return $created ? $this->responseData(201, false, "Default user has been created") : $this->responseData(500, true, "Something went wrong");
    }

    public function signUp(string $username, string $password): mixed
    {
        return $this->repo->create([
            'username' => $username,
            'password' => $password
        ]);
    }

    public function login(string $username, string $password)
    {
        $user = $this->repo->getWithUsername($username, true);
        if (!$user) return $this->responseData(404, true, "User was not found");
        if (!Password::verify($password, $user['password'])) return $this->responseData(400, true, "Invalid password");
        unset($user['password']);
        $data = [
            'user' => $user,
            'token' => JWTService::generateToken(['id' => $user['id']], ['users'])
        ];
        return $this->responseData(200, false, "User has been logged in successfully", $data);
    }
}
