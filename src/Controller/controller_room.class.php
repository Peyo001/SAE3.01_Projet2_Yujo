
<?php
    /**
     * Contrôleur pour la gestion des rooms.
     * 
     * Cette classe gère les actions liées aux rooms, telles que l'affichage,
     * la création, la modification et la suppression des rooms.
     * 
     */
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

        /**
         * Liste les rooms, éventuellement filtrées par créateur.
         * 
         * Si un identifiant de créateur (`idCreateur`) est fourni dans les paramètres GET,
         * cette méthode récupère les rooms créées par cet utilisateur. Sinon, elle récupère
         * toutes les rooms.
         * 
         * @return void
         */
        public function lister() :void{
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
        
        /**
         * Crée une nouvelle room.
         * 
         * Cette méthode gère la création d'une nouvelle room. Si le formulaire est soumis en méthode `POST`,
         * elle récupère les données, crée un objet `Room` et l'insère dans la base de données via le DAO.
         * Ensuite, l'utilisateur est redirigé vers la liste des rooms.
         * 
         * @return void
         */
        public function creer() :void{
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

        /**
         * Modifie une room existante.
         * 
         * Cette méthode permet de modifier une room existante. Si l'ID de la room est passé dans l'URL,
         * le formulaire de modification est affiché. Si le formulaire est soumis en `POST`,
         * les données sont récupérées, la room est mise à jour dans la base de données et l'utilisateur est redirigé vers la page de la room.
         * 
         * @return void
         * 
         */
        public function modifier() :void
        {
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

        /**
         * Supprime une room existante.
         * 
         * Cette méthode permet de supprimer une room existante. Si l'ID de la room est passé dans l'URL,
         * la room est supprimée de la base de données et l'utilisateur est redirigé vers la liste des rooms.
         * 
         * @return void
         */
        public function supprimer() :void
        {
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