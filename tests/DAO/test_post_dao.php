<?php
require_once __DIR__ . '/../utils.php';

$pdo = Database::getInstance()->getConnection();
$dao = new PostDao($pdo);

$all = $dao->findAll();
dumpValue('PostDao->findAll', $all);
