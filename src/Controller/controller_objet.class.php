<?php

    class ControllerObjet extends Controller {

        public function __construct(\Twig\Environment $twig, \Twig\Loader\FilesystemLoader $loader) {
            parent::__construct($twig, $loader);
        }

        public function lister() {
            $idRoom = $_GET['idRoom'] ?? null;
            $managerObjet = new ObjetDao();

            if ($idRoom) {
                $objets = $managerObjet->findByRoom($idRoom);
            }
            else {
                $objets = $managerObjet->findAll();
            }

            echo $this->getTwig()->render('(Ex:objets_list.twig)', [
                'objets' => $objets,
                'idRoom' => $idRoom
            ]);
        }

        public function afficher() {
            $idObjet = $_GET['idObjet'] ?? null;
            if (!$idObjet) {
                die("Erreur : aucun objet spécifié.");
            }

            $managerObjet = new ObjetDao();
            $objet = $managerObjet->find($idObjet);
            if (!$objet) {
                die("Objet introuvable.");
            }

            echo $this->getTwig()->render('(Ex:objet.twig)', [
                'objet' => $objet
            ]);
        }

        public function creer() {
            $idRoom = $_GET['idRoom'] ?? null;
            if (!$idRoom) {
                die("Erreur : aucune room spécifiée.");
            }

            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                echo $this->getTwig()->render('(Ex:objet_create.twig)', [
                    'idRoom' => $idRoom
                ]);
                return;
            }

            // Récupérer données du formulaire
            $description = $_POST['description'];
            $modele3dPath = $_POST['modele3dPath'];
            $prix = (int)$_POST['prix'];

            $objet = new Objet(null, $description, $modele3dPath, $prix, $idRoom);
            $managerObjet = new ObjetDao();
            $managerObjet->createObjet($objet);

            header("(Ex:Location: index.php?controller=room&action=afficher&idRoom=)".$idRoom);
            exit;
        }

        public function modifier() {
            $idObjet = $_GET['idObjet'] ?? null;
            if (!$idObjet) {
                die("Erreur : aucun objet spécifié.");
            }

            $managerObjet = new ObjetDao();
            $objet = $managerObjet->find($idObjet);
            if (!$objet) {
                die("Objet introuvable.");
            }

            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                echo $this->getTwig()->render('(Ex:objet_edit.twig)', [
                    'objet' => $objet
                ]);
                return;
            }

            $objet->setDescription($_POST['description']);
            $objet->setModele3dPath($_POST['modele3dPath']);
            $objet->setPrix((int)$_POST['prix']);

            $managerObjet->updateObjet($objet);
            header("(Ex:Location: index.php?controller=objet&action=afficher&idObjet=)".$idObjet);
            exit;
        }

        public function supprimer() {
            $idObjet = $_GET['idObjet'] ?? null;
            if (!$idObjet) {
                die("Erreur : aucun objet spécifié.");
            }

            $managerObjet = new ObjetDao();
            $objet = $managerObjet->find($idObjet);
            if (!$objet) {
                die("Objet introuvable.");
            }

            $idRoom = $objet->getIdRoom();

            $managerObjet->deleteObjet($idObjet);

            // Retour à la room
            header("(Ex:Location: index.php?controller=room&action=afficher&idRoom=)".$idRoom);
            exit;
        }
    }
