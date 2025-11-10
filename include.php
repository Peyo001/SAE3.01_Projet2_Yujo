<?php
//information de connexion a la base de donnee
require_once __DIR__ . '/config/configDatabase.php';
require_once __DIR__ . '/src/Database/DataBase.php';

// Autoloading des classes
require_once __DIR__ . '/src/Model/Signalement.class.php';
require_once __DIR__ . '/src/Model/Signalement.dao.php';
require_once __DIR__ . '/src/Model/Achat.class.php';
require_once __DIR__ . '/src/Model/Achat.dao.php';
require_once __DIR__ . '/src/Model/Room.class.php';
require_once __DIR__ . '/src/Model/Room.dao.php';
require_once __DIR__ . '/src/Model/User.class.php';
require_once __DIR__ . '/src/Model/User.dao.php';
require_once __DIR__ . '/src/Model/Post.class.php';
require_once __DIR__ . '/src/Model/Post.dao.php';
?>