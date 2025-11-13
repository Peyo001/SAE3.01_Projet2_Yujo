<?php
require_once __DIR__ . '/../include.php';
session_start();

$email = "alice@email.com"; 

$pdo = DataBase::getInstance()->getConnection();

// Récupère l'utilisateur en BDD
$stmt = $pdo->prepare("SELECT idUtilisateur FROM utilisateur WHERE email = :email");
$stmt->execute(['email' => $email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$userId = $user['idUtilisateur']; 

// Récupération des amis de l'utilisateur
$amiDao = new AmiDao();
$amis = $amiDao->findAmis($userId);

// Récupération des posts
$postDao = new PostDao();
$posts = [];
foreach ($amis as $ami) {
    $idAmi = ($ami->getIdUtilisateur1() == $userId) ? $ami->getIdUtilisateur2() : $ami->getIdUtilisateur1();

    $postsAmi = $postDao->findPostsByAuteur($idAmi);
    if (!empty($postsAmi)) {
        $posts = array_merge($posts, $postsAmi); 
    }
}

// Récupération des pseudos des amis
$utilisateurs = [];
foreach ($amis as $ami) {
    if ($ami->getIdUtilisateur1() == $userId) {
        $idAmi = $ami->getIdUtilisateur2();
    } else {
        $idAmi = $ami->getIdUtilisateur1();
    }

    $stmt = $pdo->prepare("SELECT pseudo FROM utilisateur WHERE idUtilisateur = :id");
    $stmt->execute(['id' => $idAmi]);
    $utilisateurs[$idAmi] = $stmt->fetchColumn();
}

// Affichage avec Twig
echo $twig->render('pageAccueil.html.twig', [
    'amis' => $amis,
    'posts' => $posts,
    'utilisateurs' => $utilisateurs
]);
