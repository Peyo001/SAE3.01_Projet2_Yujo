<?php
require_once __DIR__ . '/../utils.php';

$sanction = new Sanction(4, 1, 2, date('Y-m-d'), 'en_attente');
dumpValue('Sanction', $sanction);
