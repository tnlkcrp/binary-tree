<?php

use binary\TreeManager;

require 'bootstrap/bootstrap.php';

$rawArguments = $argv;
unset($rawArguments[0]);
$arguments = [];

foreach ($rawArguments as $argument) {
    $argument = explode('=', $argument);
    $arguments[$argument[0]] = $argument[1];
}

if (!isset($arguments['id'])
    || !isset($arguments['parent_id'])
    || !isset($arguments['position'])
) {
    echo "Missing argument\n";
    die;
}

$treeManager = new TreeManager($pdo);
$tree = $treeManager->move(
    $arguments['id'],
    $arguments['parent_id'],
    $arguments['position']
);
$treeManager->printTree($tree);
