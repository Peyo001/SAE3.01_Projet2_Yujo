<?php
require_once "Objet.php";

$objet = new Objet(1, "Chaise en bois", "/models/chaise.glb", 20);

echo $objet->getIdObjet();        // Doit afficher 1
echo $objet->getDescription();    // Chaise en bois
echo $objet->getModele3dPath();   // /models/chaise.glb
echo $objet->getPrix();           // 20