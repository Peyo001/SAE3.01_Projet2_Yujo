<?php
require_once __DIR__ . '/../utils.php';

$pdo = Database::getInstance()->getConnection();
$dao = new ComposerDao($pdo);

$all = $dao->findAll();
dumpValue('ComposerDao->findAll', $all);
