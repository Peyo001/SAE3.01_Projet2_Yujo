<?php

// Autoloading via Composer
require_once __DIR__ . '/vendor/autoload.php';

//information de connexion a la base de donnee
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/src/Database/DataBase.php';
require_once __DIR__ . '/config/twig.php';

// Autoloading des classes
require_once __DIR__ . '/src/Model/Signalement.class.php';
require_once __DIR__ . '/src/Model/Signalement.dao.php';
require_once __DIR__ . '/src/Model/Achat.class.php';
require_once __DIR__ . '/src/Model/Achat.dao.php';
require_once __DIR__ . '/src/Model/Room.class.php';
require_once __DIR__ . '/src/Model/Room.dao.php';
require_once __DIR__ . '/src/Model/Utilisateur.class.php';
require_once __DIR__ . '/src/Model/Utilisateur.dao.php';
require_once __DIR__ . '/src/Model/Post.class.php';
require_once __DIR__ . '/src/Model/Post.dao.php';
require_once __DIR__ . '/src/Model/Reponse.class.php';
require_once __DIR__ . '/src/Model/Reponse.dao.php';
require_once __DIR__ . '/src/Model/Sanction.class.php';
require_once __DIR__ . '/src/Model/Sanction.dao.php';
require_once __DIR__ . '/src/Model/Ami.class.php';
require_once __DIR__ . '/src/Model/Ami.dao.php';
require_once __DIR__ . '/src/Model/Objet.class.php';
require_once __DIR__ . '/src/Model/Objet.dao.php';
require_once __DIR__ . '/src/Model/Groupe.class.php';
require_once __DIR__ . '/src/Model/Groupe.dao.php';

//Chargement des controllers
require_once __DIR__ . '/src/Controller/controller.class.php';
require_once __DIR__ . '/src/Controller/controller_factory.class.php';
require_once __DIR__ . '/src/Controller/controller_post.class.php';
require_once __DIR__ . '/src/Controller/controller_signalement.class.php';
require_once __DIR__ . '/src/Controller/controller_room.class.php';
require_once __DIR__ . '/src/Controller/controller_objet.class.php';
require_once __DIR__ . '/src/Controller/controller_utilisateur.class.php';
require_once __DIR__ . '/src/Controller/controller_groupe.class.php';
?>
