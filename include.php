<?php
//information de connexion a la base de donnee
require_once __DIR__ . '/config/configDatabase.php';
require_once __DIR__ . '/src/Database/DataBase.php';

//chargement des modèles
require_once __DIR__ . '/src/Model/Post.class.php';
require_once __DIR__ . '/src/Model/Post.dao.php';
?>