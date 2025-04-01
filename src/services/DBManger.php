<?php

declare(strict_types=1);

namespace Service;

require_once __DIR__ . "/../config/settings.php";
require_once __DIR__ . "/../../vendor/autoload.php";

use Model\Image;
use Model\User;

class DBManger
{


    public static function migrate(): bool
    {
        return in_array(false, [
            (new Image())->createTbl(),
            (new User())->createTbl(),
        ]);
    }

    public static function drop(): bool
    {
        return in_array(false, [
            (new Image())->dropTbl(),
            (new User())->dropTbl(),
        ]);
    }
}
