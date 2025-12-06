<?php
require_once __DIR__ . '/../utils.php';

$achat = new Achat(3, date('Y-m-d'), 1);
dumpValue('Achat', $achat);
