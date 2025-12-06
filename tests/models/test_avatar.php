<?php
require_once __DIR__ . '/../utils.php';

$avatar = new Avatar(
    'Avatar1',
    'M',
    date('Y-m-d'),
    'peau claire',
    'brun',
    'tshirt',
    'lunettes',
    1,
    10
);

dumpValue('Avatar', $avatar);
