<?php
/**
 * Classe ControllerObjet 
 * 
 * Cette classe gère les actions liées aux objets dans l'application. Elle utilise les classes métiers et DAO appropriées
 * pour interagir avec la base de données et afficher les vues correspondantes.
 * 
 * Exemple d'utilisation :
 * $controllerObjet = new ControllerObjet($loader, $twig);
 * $controllerObjet->lister();
 * $controllerObjet->afficher();
 *
 * 
 * 
 */
    class ControllerObjet extends Controller {

        /**
         * Constructeur du contrôleur des objets.
         * 
         * Initialise la classe `ControllerObjet` en passant les objets Twig `Environment` et `FilesystemLoader`
         * au constructeur de la classe parente `Controller`.
         * 
         * @param \Twig\Loader\FilesystemLoader $loader L'objet loader pour la gestion des fichiers Twig.
         * @param \Twig\Environment $twig L'objet Twig pour le rendu des templates.
         */
        public function __construct(\Twig\Loader\FilesystemLoader $loader, \Twig\Environment $twig) {
            parent::__construct($loader, $twig);
        }

        /**
         * Liste les objets d'une room ou tous les objets si aucune room n'est spécifiée.
         * 
         * Cette méthode récupère les objets associés à une room spécifique, ou tous les objets de la base de données
         * si aucun identifiant de room n'est passé en paramètre. Ensuite, elle rend la vue `objets_list.twig` avec
         * les objets à afficher.
         * 
         * @return void
         */
        public function lister() {
            $idRoom = $_GET['idRoom'] ?? null;
            $db=Database::getInstance()->getConnection();
            $managerObjet = new ObjetDao($this->getPdo());

            if ($idRoom) {
                $objets = $managerObjet->findByRoom($idRoom);
            }
            else {
                $objets = $managerObjet->findAll();
            }

            echo $this->getTwig()->render('liste_objets.twig', [
                'objets' => $objets,
                'idRoom' => $idRoom
            ]);
        }

        /**
        * Affiche un objet spécifique.
        * 
        * Cette méthode affiche un objet spécifique en récupérant son identifiant (`idObjet`) passé dans l'URL.
        * Si l'objet est trouvé, la vue `objet.twig` est rendue avec les détails de l'objet.
        * Sinon, un message d'erreur est affiché.
        *
        * @return void
        */
        public function afficher() {
            $idObjet = $_GET['idObjet'] ?? null;
            if (!$idObjet) {
                die("Erreur : aucun objet spécifié.");
            }

            $managerObjet = new ObjetDao($this->getPdo());
            $objet = $managerObjet->find($idObjet);
            if (!$objet) {
                die("Objet introuvable.");
            }

            echo $this->getTwig()->render('objet.twig', [
                'objet' => $objet
            ]);
        }

        /**
         * Crée un nouvel objet dans une room.
         * 
         * Cette méthode gère la création d'un nouvel objet. Si le formulaire est soumis en méthode `POST`,
         * elle récupère les données, crée un objet `Objet` et l'insère dans la base de données via le DAO.
         * Ensuite, l'utilisateur est redirigé vers la page de la room.
         * 
         * @return void
         */
        public function creer() {
            $idRoom = $_GET['idRoom'] ?? null;
            if (!$idRoom) {
                die("Erreur : aucune room spécifiée.");
            }

            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                echo $this->getTwig()->render('creation_objet.twig', [
                    'idRoom' => $idRoom
                ]);
                return;
            }

            // Validation serveur des données
            $regles = [
                'description' => [
                    'obligatoire' => true,
                    'type' => 'string',
                    'longueur_min' => 2,
                    'longueur_max' => 200
                ],
                'modele3dPath' => [
                    'obligatoire' => true,
                    'type' => 'string',
                    'longueur_max' => 300,
                    // chemin simple autorisant lettres/chiffres/_-/./
                    'format' => '/^[A-Za-z0-9_\-\.\/]+$/'
                ],
                'prix' => [
                    'obligatoire' => true,
                    'type' => 'integer',
                    'plage_min' => 0
                ]
            ];

            $validator = new Validator($regles);
            $donneesValides = $validator->valider($_POST);
            if (!$donneesValides) {
                echo $this->getTwig()->render('creation_objet.twig', [
                    'idRoom' => $idRoom,
                    'erreurs' => $validator->getMessagesErreurs(),
                    'donnees' => [
                        'description' => $_POST['description'] ?? '',
                        'modele3dPath' => $_POST['modele3dPath'] ?? '',
                        'prix' => $_POST['prix'] ?? ''
                    ]
                ]);
                return;
            }

            $description = trim($_POST['description']);
            $modele3dPath = trim($_POST['modele3dPath']);
            $prix = (int)$_POST['prix'];

            $objet = new Objet(null, $description, $modele3dPath, $prix, $idRoom);
            $managerObjet = new ObjetDao($this->getPdo());
            $managerObjet->insererObjet($objet);

            header("Location: index.php?controleur=room&methode=afficher&id=".$idRoom);
            exit;
        }

        /**
         * Modifie un objet existant.
         * 
         * Cette méthode permet de modifier un objet existant. Si l'ID de l'objet est passé dans l'URL,
         * le formulaire de modification est affiché. Si le formulaire est soumis en `POST`, les données sont
         * récupérées, l'objet est mis à jour dans la base de données et l'utilisateur est redirigé vers la page de l'objet.
         * 
         * @return void
         */
        public function modifier() {
            $idObjet = $_GET['idObjet'] ?? null;
            if (!$idObjet) {
                die("Erreur : aucun objet spécifié.");
            }

            $managerObjet = new ObjetDao($this->getPdo());
            $objet = $managerObjet->find($idObjet);
            if (!$objet) {
                die("Objet introuvable.");
            }

            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                echo $this->getTwig()->render('edition_objet.twig', [
                    'objet' => $objet
                ]);
                return;
            }

            // Validation serveur des données de modification
            $regles = [
                'description' => [
                    'obligatoire' => true,
                    'type' => 'string',
                    'longueur_min' => 2,
                    'longueur_max' => 200
                ],
                'modele3dPath' => [
                    'obligatoire' => true,
                    'type' => 'string',
                    'longueur_max' => 300,
                    'format' => '/^[A-Za-z0-9_\-\.\/]+$/'
                ],
                'prix' => [
                    'obligatoire' => true,
                    'type' => 'integer',
                    'plage_min' => 0
                ]
            ];

            $validator = new Validator($regles);
            $donneesValides = $validator->valider($_POST);
            if (!$donneesValides) {
                echo $this->getTwig()->render('edition_objet.twig', [
                    'objet' => $objet,
                    'erreurs' => $validator->getMessagesErreurs(),
                    'donnees' => [
                        'description' => $_POST['description'] ?? '',
                        'modele3dPath' => $_POST['modele3dPath'] ?? '',
                        'prix' => $_POST['prix'] ?? ''
                    ]
                ]);
                return;
            }

            $objet->setDescription(trim($_POST['description']));
            $objet->setModele3dPath(trim($_POST['modele3dPath']));
            $objet->setPrix((int)$_POST['prix']);

            $managerObjet->mettreAJourObjet($objet);
            header("Location: index.php?controleur=objet&methode=afficher&idObjet=".$idObjet);
            exit;
        }

        /**
         * Modifie un objet existant.
         * 
         * Cette méthode permet de modifier un objet existant. Si l'ID de l'objet est passé dans l'URL,
         * le formulaire de modification est affiché. Si le formulaire est soumis en `POST`, les données sont
         * récupérées, l'objet est mis à jour dans la base de données et l'utilisateur est redirigé vers la page de l'objet.
         * 
         * @return void
         */
        public function supprimer() {
            $idObjet = $_GET['idObjet'] ?? null;
            if (!$idObjet) {
                die("Erreur : aucun objet spécifié.");
            }

            $managerObjet = new ObjetDao($this->getPdo());
            $objet = $managerObjet->find($idObjet);
            if (!$objet) {
                die("Objet introuvable.");
            }

            $idRoom = $objet->getIdRoom();

            $managerObjet->supprimerObjet($idObjet);

            // Retour à la room
            header("Location: index.php?controleur=room&methode=afficher&id=".$idRoom);
            exit;
        }
    }
