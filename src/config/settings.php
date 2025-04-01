<?php

require_once __DIR__ . "/config.php";


if (config('appEnv') === 'dev') {
    ini_set('display_errors', '1');
} else {
    ini_set('display_errors', '0');
}
