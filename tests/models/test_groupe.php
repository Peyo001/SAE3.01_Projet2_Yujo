<?php
require_once __DIR__ . '/../utils.php';

$groupe = new Groupe('Groupe Test', 'Description du groupe', date('Y-m-d'), [1, 2, 3], 5);
dumpValue('Groupe', $groupe);
