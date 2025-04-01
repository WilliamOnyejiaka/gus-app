<?php

declare(strict_types=1);

namespace Util;

require_once __DIR__ . "/../config/settings.php";
require_once __DIR__ . "/../../vendor/autoload.php";

class Password {

    public static function hash(string $password)
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    public static function verify(string $password,string $hashedPassword){
        return password_verify($password,$hashedPassword);
    }
}
