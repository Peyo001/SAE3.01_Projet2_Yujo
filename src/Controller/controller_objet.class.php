<?php

    class ControllerObjet extends Controller {

        public function __construct(\Twig\Loader\FilesystemLoader $loader, \Twig\Environment $twig) {
            parent::__construct($loader, $twig);
        }

        public function lister(): void {
            $idRoom = $_GET['idRoom'] ?? null;
            $managerObjet = new ObjetDao();

            if ($idRoom) {
                $objets = $managerObjet->findByRoom($idRoom);
            }
            else {
                $objets = $managerObjet->findAll();
            }

            echo $this->getTwig()->render('liste_objets.twig)', [
                'objets' => $objets,
                'title' => 'Liste des objets de la boutique'
            ]);
        }

        public function afficher(): void {
            if (!isset($_GET['idObjet'])) {
                header('Location: index.php?controleur=objet&methode=lister');
            }

            $managerObjet = new ObjetDao();
            $objet = $managerObjet->find($_GET['idObjet']);
            if (!$objet) {
                die("Objet introuvable.");
            }

            echo $this->getTwig()->render('objet.twig', [
                'objet' => $objet
            ]);
        }

        public function afficherFormulaireInsertion(): void
        {
            echo $this->getTwig()->render('ajout_objet.twig', [
                'menu' => 'nouvel_objet'
            ]);
        }

        public function traiterFormulaireInsertion() {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                header('Location: index.php?controleur=objet&methode=afficherFormulaireInsertion');
                exit;
            }

            // Récupérer données du formulaire
            $description = $_POST['description'] ?? '';
            $modele3dPath = $_POST['modele3dPath'];
            $prix = (int)$_POST['prix'];

            $objet = new Objet();
            $objet->setDescription($description);
            $objet->setModele3dPath($modele3dPath);
            $objet->setPrix($prix);
            $objet->setIdRoom($idRoom);

            $managerObjet = new ObjetDao();
            $succes = $managerObjet->createObjet($objet);

            if ($succes) {
                header('Location: index.php?controleur=objet&methode=lister');
                exit;
            }
            else {
                throw new Exception("Erreur lors de la création de l'objet.");
            }
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
