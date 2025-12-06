<?php
require_once __DIR__ . '/../utils.php';

$pdo = Database::getInstance()->getConnection();
$dao = new SignalerDao($pdo);

$all = $dao->findAll();
dumpValue('SignalerDao->findAll', $all);
