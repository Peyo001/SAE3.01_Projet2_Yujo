<?php
require_once __DIR__ . '/../utils.php';

$message = new Message(1, 'Ceci est un message', date('Y-m-d H:i:s'), 2, 1);
dumpValue('Message', $message);
