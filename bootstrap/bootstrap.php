<?php

$config = require 'config.php';
$pdo = new \PDO(
    $config['db']['dsn'],
    $config['db']['user'],
    $config['db']['password']
);

spl_autoload_register(function ($class) {
    $nameParts = explode('\\', $class);
    $shortClass = array_pop($nameParts);
    require dirname(__DIR__) . "/classes/{$shortClass}.php";
});
