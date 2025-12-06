<?php
require_once __DIR__ . '/../utils.php';

$pdo = Database::getInstance()->getConnection();
$dao = new AjouterDao($pdo);

$all = $dao->findAll();
dumpValue('AjouterDao->findAll', $all);
