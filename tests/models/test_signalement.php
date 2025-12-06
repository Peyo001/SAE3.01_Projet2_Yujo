<?php
require_once __DIR__ . '/../utils.php';

$signalement = new Signalement(4, 'Contenu inapproprié');
dumpValue('Signalement', $signalement);
