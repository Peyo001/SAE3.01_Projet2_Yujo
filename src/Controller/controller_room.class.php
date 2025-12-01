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

        public function afficher(): void {
            if (!isset($_GET['idRoom'])) {
                header('Location: index.php?controleur=room&methode=lister');
                exit;
            }

            // Récupère les Rooms à l'aide de la méthode findAll() de RoomDao
            $managerRoom = new RoomDao($this->getPdo());
            $room = $managerRoom->find($$_GET['idRoom']);

            if (!$room) {
                die("Erreur : la room n'existe pas.");
            }

            // Génération de la vue
            $template = $this->getTwig()->load('room.twig');

            echo $template->render([
                'room' => $room
            ]);
        }

        public function lister(): void {           
            $managerRoom = new RoomDao($this->getPdo());

            if (isset($_GET['idCreateur'])) {
                
                $rooms = $managerRoom->findByCreateur($_GET['idCreateur']);
            }
            else {
                $rooms = $managerRoom->findAll();
            }

            // Généralisation de la vue
            $template = $this->getTwig()->load('liste_rooms.twig');

            echo $template->render([
                'rooms' => $rooms,
                'title' => 'Liste des Rooms'
            ]);
        }

        public function afficherFormulaireInsertion(): void {
            $template = $this->getTwig()->load('ajout_room.twig');
            echo $template->render([
                'menu' => 'nouvelle_room'
            ]);
        }

        public function traiterFormulaireInsertion(): void {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                header('Location: index.php?controleur=room&methode=afficherFormulaireInsertion');
                exit;
            }

            $nom = $_POST['nom'] ?? 'Nom de la Room';
            $visibilite = $_POST['visibilite'] ?? 'privée';
            $nbVisit = $_POST['nbVisit'];
            $idCreateur = $_SESSION['idUtilisateur'];

            if (empty($nom)) {
                echo "La Room doit obligatoirement avoir un nom"; 
                return;
            }

            $room = new Room();
            $room->setNom($nom);
            $room->setVisibilite($visibilite);
            $room->setNbVisit($nbVisit);
            $room->setIdCreateur($idCreateur);

            $managerRoom = new RoomDao($this->getPdo());
            $succes = $managerRoom->createRoom($room);

            if ($succes) {
                header('Location: index.php?controleur=room&methode=lister');
                exit;
            }
            else {
                throw new Exception("Erreur lors de la création de la Room.");
            }
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