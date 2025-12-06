<?php
require_once __DIR__ . '/../utils.php';

$composer = new Composer(1, 2, date('Y-m-d H:i:s'));
dumpValue('Composer', $composer);
