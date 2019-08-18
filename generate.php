<?php

use binary\TreeManager;

require 'bootstrap/bootstrap.php';

$treeManager = new TreeManager($pdo);
$tree = $treeManager->generate();
$treeManager->printTree($tree);
