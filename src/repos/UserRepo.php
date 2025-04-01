<?php

declare(strict_types=1);

namespace Repo;

require_once __DIR__ . "/../config/settings.php";
require_once __DIR__ . "/../config/config.php";
require_once __DIR__ . "/../../vendor/autoload.php";

use Model\User;
use Lib\Serializer;

class UserRepo
{

    protected array $neededRows = ["id", "username", "created_at", "updated_at"];
    protected array $allRows = ["id", "username", "created_at", "updated_at"];
    protected User $model;

    public function __construct()
    {
        $this->allRows = ["password", ...$this->neededRows];
        $this->model = new User();
    }

    public function create(array $params)
    {
        return $this->model->insert(...$params);
    }

    public function getWithUsername(string $username,bool $allRows = false){
        return Serializer::tuple(
            $this->model->getWithUsername($username),
            $allRows ? $this->allRows : $this->neededRows
        );
    }
}

