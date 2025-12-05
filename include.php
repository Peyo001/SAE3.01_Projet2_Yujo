<?php

// Charge l'autoload Composer et les configurations de base
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/src/Database/DataBase.php';
require_once __DIR__ . '/config/twig.php';

// Charge automatiquement toutes les classes métiers, DAO et controllers selon l'arborescence du projet
$autoloadDirs = [
	__DIR__ . '/src/Model/Class',
	__DIR__ . '/src/Model/DAO',
	__DIR__ . '/src/Controller',
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
