<?php
require_once __DIR__ . '/../utils.php';

$ajouter = new Ajouter(7, 1, date('Y-m-d'));
dumpValue('Ajouter', $ajouter);
