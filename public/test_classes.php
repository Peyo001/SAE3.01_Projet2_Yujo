<?php
require_once __DIR__ . '../../include.php';

$reponse = new ReponseDao();
$reponses = $reponse->findAll();

$room = new RoomDao();
$rooms = $room->findAll();

$achat = new AchatDao();
$achats = $achat->findAll();

$post = new PostDao();
$posts = $post->findAll();

$signalement = new SignalementDao();
$signalements = $signalement->findAll();

$utilisateur = new UtilisateurDao();
$utilisateurs = $utilisateur->findAll();

$Sanction = new SanctionDao();
$sanctions = $Sanction->findAll();

$ami = new AmiDao();
$amis = $ami->findAll();

$objet = new ObjetDao();
$objets = $objet->findAll();

$groupe = new GroupeDao();
$groupes = $groupe->findAll();

foreach ($sanctions as $sanction) {
     echo "ID Signalement: " . $sanction->getIdSignalement() . "<br>";
     echo "ID Utilisateur: " . $sanction->getIdUtilisateur() . "<br>";
     echo "ID Post: " . $sanction->getIdPost() . "<br>";
     echo "Date Signalement: " . $sanction->getDateSignalement() . "<br>";
     echo "Status: " . $sanction->getStatus() . "<br><hr>";
 }

 foreach ($utilisateurs as $user) {
     echo "ID: " . $user->getIdUtilisateur() . "<br>";
     echo "Pseudo: " . $user->getPseudo() . "<br>";
     echo "Email: " . $user->getEmail() . "<br>";
     echo "Mot de passe: " . $user->getMotDePasse() . "<br>";
     echo "Type de compte: " . $user->getTypeCompte() . "<br>";
     echo "Est premium: " . ($user->getEstPremium() ? 'Oui' : 'Non') . "<br>";
     echo "Date d'inscription: " . $user->getDateInscription() . "<br>";
     echo "Yu points: " . $user->getYuPoints() . "<br>";
 }

 foreach ($signalements as $sig) {
     echo "ID: " . $sig->getId() . "<br>";
     echo "Raison: " . $sig->getRaison() . "<br><hr>";
 }

 foreach ($achats as $ach) {
     echo "ID Objet: " . $ach->getIdObjet() . "<br>";
     echo "Date d'Achat: " . $ach->getDateAchat() . "<br>";
     echo "ID Utilisateur: " . $ach->getIdUtilisateur() . "<br><hr>";
 }

 foreach ($posts as $pt) {
     echo "ID: " . $pt->getIdPost() . "<br>";
     echo "Date: " . $pt->getDatePublication() . "<br>";
     echo "Contenu: " . $pt->getContenu() . "<br>";
     echo "Auteur ID: " . $pt->getIdAuteur() . "<br>";
     echo "Room ID: " . $pt->getIdRoom() . "<br><hr>";
 }


 foreach ($rooms as $rm) {
     echo "ID: " . $rm->getIdRoom() . "<br>";
     echo "Nom: " . $rm->getNom() . "<br>";
     echo "Visibilite: " . $rm->getVisibilite() . "<br>";
     echo "Date de Creation: " . $rm->getDateCreation() . "<br>";
     echo "Nombre de Visite: " . $rm->getNbVisit() . "<br>";
     echo "ID Createur: " . $rm->getIdCreateur() . "<br><hr>";
 }

 foreach ($reponses as $rep) {
     echo "ID: " . $rep->getId() . "<br>";
     echo "Date: " . $rep->getDateReponse() . "<br>";
     echo "Contenu: " . $rep->getContenu() . "<br>";
     echo "Auteur ID: " . $rep->getIdAuteur() . "<br>";
     echo "Post ID: " . $rep->getIdPost() . "<br><hr>";
 }

foreach ($amis as $ami) {
    echo "ID user 1: " . $ami->getIdUtilisateur1() . "<br>";
    echo "ID user 2: " . $ami->getIdUtilisateur2() . "<br>";
    echo "Date Ajout: " . $ami->getDateAjout() . "<br><hr>";
}

foreach ($objets as $objet) {
    echo "ID Objet: " . $objet->getIdObjet() . "<br>";
    echo "Description: " . $objet->getDescription() . "<br>";
    echo "Modèle 3D Path: " . $objet->getModele3dPath() . "<br>";
    echo "Prix: " . $objet->getPrix() . "<br><hr>";
}
foreach ($groupes as $groupe) {
    echo "ID Groupe: " . $groupe->getIdGroupe() . "<br>";
    echo "Nom: " . $groupe->getNomGroupe() . "<br>";
    echo "Description: " . $groupe->getDescriptionGroupe() . "<br>";
    echo "Date Creation: " . $groupe->getDateCreation() . "<br>";
    echo "Membres: " . implode(", ", $groupe->getMembres()) . "<br><hr>";
}
?>