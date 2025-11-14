<?php
//information de connexion a la base de donnee
require_once __DIR__ . '/config/configDatabase.php';
require_once __DIR__ . '/src/Database/DataBase.php';
require_once __DIR__ . '/config/twig.php';

// Autoloading des classes DAO
require_once __DIR__ . '/src/Model/Utilisateur.dao.php';
require_once __DIR__ . '/src/Model/Groupe.dao.php';
require_once __DIR__ . '/src/Model/Message.dao.php';
require_once __DIR__ . '/src/Model/Amis.dao.php';
require_once __DIR__ . '/src/Model/Signalement.dao.php';
require_once __DIR__ . '/src/Model/Sanction.dao.php';
require_once __DIR__ . '/src/Model/Romm.dao.php';
require_once __DIR__ . '/src/Model/Reponse.dao.php';
require_once __DIR__ . '/src/Model/Post.dao.php';
require_once __DIR__ . '/src/Model/Achat.dao.php';

// Autoloading des classes Métier

require_once __DIR__ . '/src/Model/Utilisateur.class.php';
require_once __DIR__ . '/src/Model/Groupe.class.php';
require_once __DIR__ . '/src/Model/Message.class.php';
require_once __DIR__ . '/src/Model/Amis.class.php';
require_once __DIR__ . '/src/Model/Signalement.class.php';
require_once __DIR__ . '/src/Model/Sanction.class.php';
require_once __DIR__ . '/src/Model/Romm.class.php';
require_once __DIR__ . '/src/Model/Reponse.class.php';
require_once __DIR__ . '/src/Model/Post.class.php';
require_once __DIR__ . '/src/Model/Achat.class.php';

?>

