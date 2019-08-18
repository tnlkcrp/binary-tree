<?php

$config = require 'config.php';

try {
    $pdo = new \PDO(
        $config['db']['dsn'],
        $config['db']['user'],
        $config['db']['password']
    );
} catch (\Exception $e) {
    echo "Database error, check config and connection to database.\n";
    echo $e->getMessage() . "\n";
    die;
}

spl_autoload_register(function ($class) {
    $nameParts = explode('\\', $class);
    $shortClass = array_pop($nameParts);
    require dirname(__DIR__) . "/classes/{$shortClass}.php";
});
