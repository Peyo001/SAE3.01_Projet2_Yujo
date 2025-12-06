<?php
require_once __DIR__ . '/../utils.php';

$posseder = new Posseder(8, 1, date('Y-m-d'));
dumpValue('Posseder', $posseder);
