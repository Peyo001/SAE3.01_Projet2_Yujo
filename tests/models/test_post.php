<?php
require_once __DIR__ . '/../utils.php';

$post = new Post(null, 'Bonjour tout le monde !', 'texte', date('Y-m-d H:i:s'), 1, 1);
dumpValue('Post', $post);
