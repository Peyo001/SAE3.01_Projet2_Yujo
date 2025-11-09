<?php
require_once __DIR__ . '/../include.php';


// Récupérer la connexion PDO
$pdo = Database::getInstance()->getConnection();

// Créer le DAO
$postDao = new PostDao($pdo);

// Récupérer tous les posts existants
$allPosts = $postDao->findAll();;

if (empty($allPosts)) {
    echo "Aucun post trouvé.<br>";
} else {
    echo "Nombre total de posts : " . count($allPosts) . "<br>\n";
    foreach ($allPosts as $post) {
        echo "- " . $post->getContenu() . " (ID: " . $post->getIdPost() . ")<br>\n";
    }
}

// Exemple de récupération d’un post par ID (ici ID = 1)
$retrievedPost = $postDao->find(101);
if ($retrievedPost) {
    echo "\nPost ID 101 : " . $retrievedPost->getContenu() . "<br>\n";
} else {
    echo "\nAucun post trouvé avec l’ID 101.<br>\n";
}
