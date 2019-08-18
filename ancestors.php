<?php

use binary\TreeManager;

require 'bootstrap/bootstrap.php';

if (!isset($argv[1])) {
    echo "Missing argument \n";
    die;
}

$treeManager = new TreeManager($pdo);
$tree = $treeManager->getAncestorsById(intval($argv[1]));
$treeManager->printTree($tree);
