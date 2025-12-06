<?php
require_once __DIR__ . '/../utils.php';

$room = new Room(null, 'Ma room', 'public', date('Y-m-d'), 0, 1, '{"palette":"sunset"}');
dumpValue('Room', $room);
