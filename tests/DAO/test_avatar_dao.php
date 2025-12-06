<?php
require_once __DIR__ . '/../utils.php';

$pdo = Database::getInstance()->getConnection();
$dao = new AvatarDao($pdo);

$all = $dao->findAll();
dumpValue('AvatarDao->findAll', $all);
