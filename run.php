<?php

use binary\TreeManager;

$config = require 'config/db.php';
$pdo = new \PDO($config['dsn'], $config['user'], $config['password']);

spl_autoload_register(function ($class) {
    $nameParts = explode('\\', $class);
    $shortClass = array_pop($nameParts);
    include "classes/{$shortClass}.php";
});

$treeManager = new TreeManager($pdo);
$treeManager->generate();
