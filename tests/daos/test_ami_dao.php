<?php
require_once __DIR__ . '/../utils.php';

$pdo = Database::getInstance()->getConnection();
$dao = new AmiDao($pdo);

$all = $dao->findAll();
dumpValue('AmiDao->findAll', $all);
