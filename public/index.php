<?php   
require_once __DIR__ . '/../include.php';

// Connexion BDD
$pdo = DataBase::getInstance()->getConnection();

// Récupération données
$ami = new UtilisateurDao();
$amis = $ami->findAll();
$post = new PostDao();
$posts = $post->findAll();
// Affichage Twig
echo $twig->render('pageAccueil.html.twig', [
    'amis' => $amis,
    'posts' => $posts,
]);
?>
