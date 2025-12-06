<?php
require_once __DIR__ . '/../utils.php';

$reponse = new Reponse(null, date('Y-m-d H:i:s'), "hello", 2, 1);
dumpValue('Reponse', $reponse);
