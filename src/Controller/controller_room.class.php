<!-- Controller pour la classe Room -->

<!-- La classe Room intéragit avec :
        - la page d'accueil (quand un utilisateur post une room ou en modifie une, il peut apparaître sur notre page d'accueil)
        - la page de profil (un utilisateur peut accéder à sa room depuis sa page d'accueil)
        - la page de personnalisation d'une room car on a besoin de la room pour pouvoir la modifier -->

<?php
    class ControllerRoom extends Controller {
        public function __construct(\Twig\Loader\FilesystemLoader $loader, \Twig\Environment $twig) {
            parent::__construct($loader, $twig);
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
            echo $this->getTwig()->render('room.twig', [
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
            echo $this->getTwig()->render('rooms_list.twig', [
                'rooms' => $rooms,
                'idCreateur' => $idCreateur
            ]);
        }

        public function creer() {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                echo $this->getTwig()->render('room_create.twig');
                return;
            }

            $nom = $_POST['nom'];
            $visibilite = $_POST['visibilite'];
            $idCreateur = $_SESSION['idUtilisateur'];   // a modifier, en liant la classe UTILISATEUR

            $room = new Room(
                null,
                $nom,
                $visibilite,
                date('Y-m-d'),
                0,
                $idCreateur,
                null
            );

            $managerRoom = new RoomDao($this->getPdo());
            $managerRoom->insererRoom($room);

            header("Location: index.php?controleur=room&methode=lister");
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
                echo $this->getTwig()->render('room_edit.twig', [
                    'room' => $room
                ]);
                return;
            }

            $room->setNom($_POST['nom']);
            $room->setVisibilite($_POST['visibilite']);

            $managerRoom->update($room);

            header("Location: index.php?controleur=room&methode=afficher&id=".$idRoom);
            exit;
        }

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
    }