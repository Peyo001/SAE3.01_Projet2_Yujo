<?php
require_once __DIR__ . '/../utils.php';

$pdo = Database::getInstance()->getConnection();
$dao = new ObjetDao($pdo);

$all = $dao->findAll();
dumpValue('ObjetDao->findAll', $all);
