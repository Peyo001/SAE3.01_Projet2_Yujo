<?php
require_once __DIR__ . '/../utils.php';

$objet = new Objet(null, 'Chaise en bois', '/models/chaise.obj', 100, null);
dumpValue('Objet', $objet);
