<?php

// Démarre la session si elle n'est pas déjà active, avec des paramètres sûrs
if (session_status() === PHP_SESSION_NONE) {
	$secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
	if (PHP_VERSION_ID >= 70300) {
		session_set_cookie_params([
			'lifetime' => 0,
			'path' => '/',
			'secure' => $secure,
			'httponly' => true,  
			'samesite' => 'Lax',
		]);
	} else {
		session_set_cookie_params(0, '/; samesite=Lax', '', $secure, true);
	}
	session_start();
}

// Charge l'autoload Composer et les configurations de base
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/src/Database/DataBase.php';
require_once __DIR__ . '/config/twig.php';

//Charge la classe de validation
require_once __DIR__ . '/Validator.class.php';

// Charge automatiquement toutes les classes métiers, DAO et controllers selon l'arborescence du projet
$autoloadDirs = [
	__DIR__ . '/src/Model/Class',
	__DIR__ . '/src/Model/DAO',
	__DIR__ . '/src/Controller',
	__DIR__ . '/src/Service',
];


// Parcours les répertoires spécifiés et inclut tous les fichiers PHP qu'ils contiennent
foreach ($autoloadDirs as $dir) {
	if (!is_dir($dir)) {
		continue;
	}

    // Inclut tous les fichiers PHP dans le répertoire courant
	foreach (glob($dir . '/*.php') as $file) {
		require_once $file;
	}
}

?>
