<?php
require_once __DIR__ . '/../utils.php';

$pdo = Database::getInstance()->getConnection();
$dao = new SanctionDao($pdo);

$all = $dao->findAll();
dumpValue('SanctionDao->findAll', $all);
