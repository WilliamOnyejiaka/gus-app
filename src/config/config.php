<?php

function config($key)
{
    $host = $_ENV['DB_HOST'] ?? "127.0.0.1";
    $dbName = $_ENV['DB_NAME'] ?? "gus_db";
    $username = $_ENV['DB_USERNAME'] ?? "root";
    $port = $_ENV['PORT'] ?? 3306;
    $password = $_ENV['DB_PASSWORD'] ?? "";
    $onDev = isset($_SERVER['SERVER_ADDR']) && ($_SERVER['SERVER_ADDR'] === '127.0.0.1' || $_SERVER['SERVER_ADDR'] === '::1');
    $adminUsername = $_ENV['ADMIN_USERNAME'] ?? "ranker";
    $adminPassword = $_ENV['ADMIN_PASSWORD'] ?? "jinwoo";


    return ([
        'allow_cors' => false,
        'secretKey' => $_ENV['SECRET_KEY'] ?? "itsasecret",
        'hash' => $_ENV['HASH'] ?? "HS512",
        'connectVars' => [
            'host' => $host,
            'username' => $username,
            'password' => $password,
            'dbName' => $dbName,
            'port' => $port
        ],
        'username' => $adminUsername,
        'password' => $adminPassword,
        'iv' => $_ENV['IV'] ?? "1234567892345678",
        'appEnv' => $onDev ? "dev" : "prod"
    ])[$key];
}
