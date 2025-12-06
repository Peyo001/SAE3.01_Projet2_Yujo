<?php
require_once __DIR__ . '/../utils.php';

$pdo = Database::getInstance()->getConnection();
$dao = new PossederDAO($pdo);

$all = $dao->findAll();
dumpValue('PossederDAO->findAll', $all);
