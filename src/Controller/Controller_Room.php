<!-- Controller pour la classe Room -->

<!-- La classe Room intéragit avec :
        - la page d'accueil (quand un utilisateur post une room ou en modifie une, il peut apparaître sur notre page d'accueil)
        - la page de profil (un utilisateur peut accéder à sa room depuis sa page d'accueil)
        - la page de personnalisation d'une room car on a besoin de la room pour pouvoir la modifier -->

<?php
    class ControllerRoom extends Controller {
        public function __construct(\Twig\Environment $twig, \Twig\Loader\FilesystemLoader $loader) {
            parent::__construct($twig, $loader);
        }

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
            $template = $this->getTwig()->load('fichier twig de la room (Ex:room.twig)');

            echo $template->render([
                'room' => $room
            ]);
        }

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
            $template = $this->getTwig()->load('fichier twig (Ex:rooms_list.twig');

            echo $template->render([
                'rooms' => $rooms,
                'idCreateur' => $idCreateur
            ]);
        }

        public function creer() {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                echo $this->getTwig()->render('Fichier twig (Ex:room_create.twig)');
                return;
            }

            $nom = $_POST['nom'];
            $visibilite = $_POST['visibilite'];
            $idCreateur = $_SESSION['idUtilisateur'];   // a modifier, en liant la classe UTILISATEUR

            $room = new Room(
                null,
                $nom,
                $visibilite,
                date('YYYY-mm-dd'),
                0,
                $idCreateur
            );

            $managerRoom = new RoomDao($this->getPdo());
            $managerRoom->createRoom($room);

            header("(Ex:Location: index.php?controller=room&action=lister)");
            exit;
        }

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
                echo $this->getTwig()->render('(Ex:room_edit.twig)', [
                    'room' => $room
                ]);
                return;
            }

            $room->setNom($_POST['nom']);
            $room->setVisibilite($_POST['visibilite']);

            $managerRoom->updateRoom($room);

            header("(Ex:Location: index.php?controller=room&action=afficher&idRoom=)".$idRoom);
            exit;
        }

        public function supprimer() {
            $idRoom = $_GET['idRoom'] ?? null;

            if (!$idRoom) {
                die("Aucune room à supprimer.");
            }

            $managerRoom = new RoomDao($this->getPdo());
            $managerRoom->deleteRoom($idRoom);

            header("(Ex:Location: index.php?controller=room&action=lister)");
            exit;
        }
    }