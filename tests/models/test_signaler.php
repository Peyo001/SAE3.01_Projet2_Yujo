<?php
require_once __DIR__ . '/../utils.php';

$signaler = new Signaler(1, 3, 5, date('Y-m-d H:i:s'), 'signalé');
dumpValue('Signaler', $signaler);
