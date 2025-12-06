<?php
require_once __DIR__ . '/../utils.php';

$pdo = Database::getInstance()->getConnection();
$dao = new AchatDao($pdo);

$all = $dao->findAll();
dumpValue('AchatDao->findAll', $all);
