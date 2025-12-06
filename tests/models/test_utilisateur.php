<?php
require_once __DIR__ . '/../utils.php';

$user = new Utilisateur(
    'Dupont',
    'Jean',
    '2000-01-01',
    'M',
    'jdupont',
    'jean@email.com',
    password_hash('pass', PASSWORD_DEFAULT),
    'standard',
    false,
    date('Y-m-d'),
    42,
    1,
    '{"theme":"dark"}'
);

dumpValue('Utilisateur', $user);
