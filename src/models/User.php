<?php

declare(strict_types=1);

namespace Model;

require_once __DIR__ . "/../config/settings.php";
require_once __DIR__ . "/../../vendor/autoload.php";
include_once __DIR__ . "/../config/config.php";


use Lib\Model;

class User extends Model
{

    public function __construct()
    {
        parent::__construct(...config('connectVars'));
        $this->tblName = "users";
        $this->createQuery = "CREATE TABLE IF NOT EXISTS $this->tblName (
            id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) UNIQUE NOT NULL,
            password TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )";
    }

    public function insert(string $username, string $password)
    {
        $sql = "INSERT INTO $this->tblName(username,password) VALUES(?,?)";

        $username = parent::sanitize($username);
        $password = parent::sanitize($password);

        return parent::affectRowQuery($sql, [$username, $password]);
    }

    public function getWithUsername(string $username)
    {
        $sql = "SELECT * FROM $this->tblName WHERE username = ?";
        $username = parent::sanitize($username);

        return parent::queryWithParams($sql, [$username]);
    }
}
