<?php
require_once __DIR__ . '/../utils.php';

$pdo = Database::getInstance()->getConnection();
$dao = new MessageDAO($pdo);

$all = $dao->findAll();
dumpValue('MessageDAO->findAll', $all);
