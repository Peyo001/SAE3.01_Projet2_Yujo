<?php
    /**
     * Controller pour la classe Room

     * La classe Room intéragit avec :
     * - la page d'accueil (quand un utilisateur post une room ou en modifie une, il peut apparaître sur notre page d'accueil)
     * - la page de profil (un utilisateur peut accéder à sa room depuis sa page d'accueil)
     * - la page de personnalisation d'une room car on a besoin de la room pour pouvoir la modifier

     * 
     * ControllerRoom, gère les actions liées aux rooms comme
     * l'affichage, la création, la modification et la suppression d'une room.
     * 
     * Hérite de la classe Controller pour bénéficier des fonctionnalités de base.
     * Utilise Twig pour le rendu des vues.
     * Utilise la classe Validator pour la validation des données.
     * 
     * Exemples d'utilisation :
     * $controllerRoom = new ControllerRoom($loader, $twig);
     * $controllerRoom->afficher(); // affiche une room spécifique
     * $controllerRoom->lister(); // affiche la liste des rooms
     * $controllerRoom->creer(); // crée une nouvelle room
     * $controllerRoom->modifier(); // modifie une room existante
     * $controllerRoom->supprimer(); // supprime une room
     */
    class ControllerRoom extends Controller {

        /**
         * @brief Constructeur de la classe ControllerRoom.
         * 
         * @param \Twig\Loader\FilesystemLoader $loader Le chargeur de templates Twig.
         * @param \Twig\Environment $twig L'environnement Twig pour le rendu des vues.
         */
        public function __construct(\Twig\Loader\FilesystemLoader $loader, \Twig\Environment $twig) {
            parent::__construct($loader, $twig);
        }

        /**
         * @brief Affiche une room spécifique.
         * 
         * Récupère l'idRoom depuis les paramètres GET.
         * Utilise RoomDao pour récupérer les informations de la room.
         * Rend la vue 'room.twig' avec les données de la room.
         * 
         * @return void
         */
        public function afficher() {
            $idRoom = isset($_GET['idRoom']) ? $_GET['idRoom'] : null;

            if ($idRoom === null) {
                die("Erreur : aucun idRoom fourni.");
            }

            // Récupère les Rooms à l'aide de la méthode findAll() de RoomDao
            $managerRoom = new RoomDao($this->getPdo());
            $room = $managerRoom->find($idRoom);

            if (!$room) {
                die("Erreur : la room n'existe pas.");
            }

            // Génération de la vue
            echo $this->getTwig()->render('room.twig', [
                'room' => $room
            ]);
        }

        /**
         * @brief Affiche une room en 3D (Three.js) pour prévisualisation.
         */
        public function afficherThreejs() {
            $idRoom = isset($_GET['idRoom']) ? $_GET['idRoom'] : null;

            if ($idRoom === null) {
                header('Location: index.php?controleur=room&methode=lister');
                exit;
            }

            $managerRoom = new RoomDao($this->getPdo());
            $room = $managerRoom->find((int)$idRoom);

            if (!$room) {
                header('Location: index.php?controleur=room&methode=lister');
                exit;
            }

            // Récupère les objets de la room avec leurs positions 3D
            $objetsAvecPositions = $managerRoom->findObjetsRoomAvecPositions((int)$idRoom);

            // Récupère les objets achetés disponibles (non encore placés dans la room)
            $achatDao = new AchatDao($this->getPdo());
            $idUtilisateur = $_SESSION['idUtilisateur'] ?? null;
            $objetsDisponibles = [];
            
            if ($idUtilisateur && $room->getIdCreateur() == $idUtilisateur) {
                $objetsDisponibles = $achatDao->findObjetsAchetesNonPlaces((int)$idUtilisateur, (int)$idRoom);
            }

            echo $this->getTwig()->render('room_threejs_new.twig', [
                'room' => $room,
                'objetsPlaces' => $objetsAvecPositions,
                'objetsDisponibles' => $objetsDisponibles,
                'estProprietaire' => ($idUtilisateur && $room->getIdCreateur() == $idUtilisateur)
            ]);
        }

        /**
         * @brief Affiche la liste des rooms.
         * 
         * Récupère l'idCreateur depuis les paramètres GET (optionnel).
         * Utilise RoomDao pour récupérer toutes les rooms ou celles d'un créateur spécifique.
         * Rend la vue 'liste_rooms.twig' avec les données des rooms.
         * 
         * @return void
         */
        public function lister() {
            $idCreateur = isset($_GET['idCreateur']) ? $_GET['idCreateur'] : null;            
            $managerRoom = new RoomDao($this->getPdo());

            if ($idCreateur === null) {
                $rooms = $managerRoom->findAll();
            }
            else {
                $rooms = $managerRoom->findByCreateur($idCreateur);
            }

            // Généralisation de la vue
            echo $this->getTwig()->render('liste_rooms.twig', [
                'rooms' => $rooms,
                'idCreateur' => $idCreateur
            ]);
        }

        /**
         * @brief Crée une nouvelle room.
         * 
         * Affiche le formulaire de création si la requête n'est pas en POST.
         * Sinon, récupère les données du formulaire, crée une nouvelle room et la sauvegarde.
         * Redirige vers la liste des rooms après création.
         * 
         * @return void
         */
        public function creer() {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                echo $this->getTwig()->render('creation_room.twig');
                return;
            }

            $nom = $this->sanitize($_POST['nom']);
            $visibilite = $this->sanitize($_POST['visibilite']);
            $idCreateur = $_SESSION['idUtilisateur'];   // a modifier, en liant la classe UTILISATEUR

            $room = new Room(
                null,            // idRoom
                $nom,            // nom
                $visibilite,     // visibilite
                date('Y-m-d'),   // dateCreation
                0,               // nbVisit
                (int)$idCreateur,// idCreateur
                null             // personnalisation
            );

            $managerRoom = new RoomDao($this->getPdo());
            $managerRoom->creerRoom($room);

            header("Location: index.php?controleur=room&methode=afficherThreejs&idRoom=" . $room->getIdRoom());
            exit;
        }

        /**
         * @brief Modifie une room existante.
         * 
         * Récupère l'idRoom depuis les paramètres GET.
         * Affiche le formulaire de modification si la requête n'est pas en POST.
         * Sinon, récupère les données du formulaire, met à jour la room et la sauvegarde.
         * Redirige vers l'affichage de la room après modification.
         * 
         * @return void
         */
        public function modifier() {
            $idRoom = $_GET['idRoom'] ?? null;

            if (!$idRoom) {
                die("Aucune room à modifier.");
            }

            $managerRoom = new RoomDao($this->getPdo());
            $room = $managerRoom->find($idRoom);

            if (!$room) {
                die("Room introuvable.");
            }

            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                echo $this->getTwig()->render('edition_room.twig', [
                    'room' => $room
                ]);
                return;
            }

            $room->setNom($this->sanitize($_POST['nom']));
            $room->setVisibilite($this->sanitize($_POST['visibilite']));

            $managerRoom->update($room);

            header("Location: index.php?controleur=room&methode=afficher&id=".$idRoom);
            exit;
        }

        /**
         * @brief Supprime une room existante.
         * 
         * Récupère l'idRoom depuis les paramètres GET.
         * Supprime la room correspondante.
         * Redirige vers la liste des rooms après suppression.
         * 
         * @return void
         */
        public function supprimer() {
            $idRoom = $_GET['idRoom'] ?? null;

            if (!$idRoom) {
                die("Aucune room à supprimer.");
            }

            $managerRoom = new RoomDao($this->getPdo());
            $managerRoom->supprimerRoom($idRoom);

            header("Location: index.php?controleur=room&methode=lister");
            exit;
        }

        public function rejoindre() {
            
            // A implémenter plus tard
            exit;
        }

        /**
         * @brief Ajoute un objet acheté dans la room avec une position 3D.
         * 
         * Méthode appelée en AJAX pour placer un objet dans la room.
         * Vérifie que l'utilisateur a acheté l'objet et qu'il est propriétaire de la room.
         * 
         * @return void (renvoie du JSON)
         */
        public function ajouterObjetDansRoom() {
            header('Content-Type: application/json');
            
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
                exit;
            }

            $idRoom = (int)($_POST['idRoom'] ?? 0);
            $idObjet = (int)($_POST['idObjet'] ?? 0);
            $positionX = (float)($_POST['positionX'] ?? 0.0);
            $positionY = (float)($_POST['positionY'] ?? 0.0);
            $positionZ = (float)($_POST['positionZ'] ?? 0.0);
            $rotationX = (float)($_POST['rotationX'] ?? 0.0);
            $rotationY = (float)($_POST['rotationY'] ?? 0.0);
            $rotationZ = (float)($_POST['rotationZ'] ?? 0.0);
            $scale = (float)($_POST['scale'] ?? 1.0);

            $idUtilisateur = $_SESSION['idUtilisateur'] ?? null;

            if (!$idUtilisateur) {
                echo json_encode(['success' => false, 'message' => 'Non authentifié']);
                exit;
            }

            // Vérifie que la room existe et appartient à l'utilisateur
            $managerRoom = new RoomDao($this->getPdo());
            $room = $managerRoom->find($idRoom);

            if (!$room || $room->getIdCreateur() != $idUtilisateur) {
                echo json_encode(['success' => false, 'message' => 'Room non trouvée ou non autorisée']);
                exit;
            }

            // Vérifie que l'utilisateur a acheté l'objet
            $achatDao = new AchatDao($this->getPdo());
            $achat = $achatDao->findByObjetUtilisateur($idObjet, (int)$idUtilisateur);

            if (!$achat) {
                echo json_encode(['success' => false, 'message' => 'Objet non acheté']);
                exit;
            }

            // Ajoute l'objet dans la room avec sa position
            $success = $managerRoom->ajouterObjetAvecPosition(
                $idRoom, $idObjet, $positionX, $positionY, $positionZ,
                $rotationX, $rotationY, $rotationZ, $scale
            );

            if ($success) {
                echo json_encode(['success' => true, 'message' => 'Objet ajouté à la room']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'ajout']);
            }
            exit;
        }

        /**
         * @brief Met à jour la position d'un objet dans la room.
         * 
         * Méthode appelée en AJAX pour déplacer/faire pivoter/redimensionner un objet.
         * 
         * @return void (renvoie du JSON)
         */
        public function updateObjetPosition() {
            header('Content-Type: application/json');
            
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
                exit;
            }

            $idRoom = (int)($_POST['idRoom'] ?? 0);
            $idObjet = (int)($_POST['idObjet'] ?? 0);
            $positionX = (float)($_POST['positionX'] ?? 0.0);
            $positionY = (float)($_POST['positionY'] ?? 0.0);
            $positionZ = (float)($_POST['positionZ'] ?? 0.0);
            $rotationX = (float)($_POST['rotationX'] ?? 0.0);
            $rotationY = (float)($_POST['rotationY'] ?? 0.0);
            $rotationZ = (float)($_POST['rotationZ'] ?? 0.0);
            $scale = (float)($_POST['scale'] ?? 1.0);

            $idUtilisateur = $_SESSION['idUtilisateur'] ?? null;

            if (!$idUtilisateur) {
                echo json_encode(['success' => false, 'message' => 'Non authentifié']);
                exit;
            }

            // Vérifie que la room existe et appartient à l'utilisateur
            $managerRoom = new RoomDao($this->getPdo());
            $room = $managerRoom->find($idRoom);

            if (!$room || $room->getIdCreateur() != $idUtilisateur) {
                echo json_encode(['success' => false, 'message' => 'Room non trouvée ou non autorisée']);
                exit;
            }

            // Met à jour la position de l'objet
            $success = $managerRoom->updateObjetPosition(
                $idRoom, $idObjet, $positionX, $positionY, $positionZ,
                $rotationX, $rotationY, $rotationZ, $scale
            );

            if ($success) {
                echo json_encode(['success' => true, 'message' => 'Position mise à jour']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Erreur lors de la mise à jour']);
            }
            exit;
        }

        /**
         * @brief Retire un objet de la room.
         * 
         * Méthode appelée en AJAX pour retirer un objet de la room.
         * L'objet reste dans l'inventaire de l'utilisateur.
         * 
         * @return void (renvoie du JSON)
         */
        public function retirerObjetDeRoom() {
            header('Content-Type: application/json');
            
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
                exit;
            }

            $idRoom = (int)($_POST['idRoom'] ?? 0);
            $idObjet = (int)($_POST['idObjet'] ?? 0);
            $idUtilisateur = $_SESSION['idUtilisateur'] ?? null;

            if (!$idUtilisateur) {
                echo json_encode(['success' => false, 'message' => 'Non authentifié']);
                exit;
            }

            // Vérifie que la room existe et appartient à l'utilisateur
            $managerRoom = new RoomDao($this->getPdo());
            $room = $managerRoom->find($idRoom);

            if (!$room || $room->getIdCreateur() != $idUtilisateur) {
                echo json_encode(['success' => false, 'message' => 'Room non trouvée ou non autorisée']);
                exit;
            }

            // Retire l'objet de la room
            $success = $managerRoom->retirerObjetDeRoom($idRoom, $idObjet);

            if ($success) {
                echo json_encode(['success' => true, 'message' => 'Objet retiré de la room']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Erreur lors du retrait']);
            }
            exit;
        }
    }