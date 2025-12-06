<?php
require_once __DIR__ . '/../utils.php';

$pdo = Database::getInstance()->getConnection();
$dao = new RoomDao($pdo);

$all = $dao->findAll();
dumpValue('RoomDao->findAll', $all);
$public = $dao->findPublicRooms();
dumpValue('RoomDao->findPublicRooms', $public);
