<?php

use binary\TreeManager;

require 'bootstrap/bootstrap.php';

$treeManager = new TreeManager($pdo);
$tree = $treeManager->getChildrenById($argv[1]);
$treeManager->printTree($tree);
