<?php
require_once __DIR__ . '/../utils.php';

$pdo = Database::getInstance()->getConnection();
$dao = new ReponseDao($pdo);

$all = $dao->findAll();
dumpValue('ReponseDao->findAll', $all);
