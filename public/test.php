<?php
require_once __DIR__ . '/../include.php';

$pdo = Database::getInstance()->getConnection();

$postDao = new PostDao($pdo);

$newPost = new Post();
$newPost->setContenu("Post de test manuel");
$newPost->setTypePost("texte");
$newPost->setDatePublication(date('Y-m-d H:i:s'));
$newPost->setIdAuteur(1);
$newPost->setIdRoom(10);

try {
    if ($postDao->createPost($newPost)) {
        echo "Post créé avec succès, ID : " . $newPost->getIdPost() . "<br>\n";
    } else {
        echo "Échec de la création du post<br>\n";
    }
} catch (PDOException $e) {
    echo "Erreur PDO : " . $e->getMessage() . "<br>\n";
}

echo "<h3>Posts après création :</h3>";
$allPosts = $postDao->findAll();
if (empty($allPosts)) {
    echo "Aucun post trouvé.<br>";
} else {
    foreach ($allPosts as $post) {
        echo "- " . $post->getContenu() . " (ID: " . $post->getIdPost() . ")<br>\n";
    }
}

try {
    if ($postDao->deletePost($newPost->getIdPost())) {
        echo "<br>Post supprimé avec succès<br>\n";
    } else {
        echo "<br>Échec de la suppression<br>\n";
    }
} catch (PDOException $e) {
    echo "<br>Erreur PDO lors de la suppression : " . $e->getMessage() . "<br>\n";
}

echo "<h3>Posts après suppression :</h3>";
$allPostsAfter = $postDao->findAll();
if (empty($allPostsAfter)) {
    echo "Aucun post trouvé.<br>";
} else {
    foreach ($allPostsAfter as $post) {
        echo "- " . $post->getContenu() . " (ID: " . $post->getIdPost() . ")<br>\n";
    }
}
