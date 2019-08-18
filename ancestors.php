<?php

use binary\TreeManager;

require 'bootstrap/bootstrap.php';

$treeManager = new TreeManager($pdo);
$treeManager->getAncestorsById($argv[1]);
