<?php
require_once __DIR__ . '/../utils.php';

$pdo = Database::getInstance()->getConnection();
$dao = new GroupeDao($pdo);

$all = $dao->findAll();
dumpValue('GroupeDao->findAll', $all);
