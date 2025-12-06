<?php
require_once __DIR__ . '/../utils.php';

$ami = new Ami(1, 2, date('Y-m-d'));
dumpValue('Ami', $ami);
