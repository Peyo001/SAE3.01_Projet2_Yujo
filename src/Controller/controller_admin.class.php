<?php

    class ControllerAdmin extends Controller {
        
        /*private function verifierAdmin(): void {
            if (!isset($_SESSION['user'])) {
                exit('DEBUG : pas de session user');
            }
            if ($_SESSION['user']['role'] !== 'admin') {
                exit('DEBUG : pas admin');
            }
        }*/

        public function dashboard(): void {
            //$this->verifierAdmin();

            $userDAO = new UtilisateurDAO($this->getPdo());
            $users = $userDAO->findAll();

            echo $this->getTwig()->render('admin/dashboard.twig', [
                'users' => $users
            ]);
        }

        public function ajouterObjet(): void {
            //$this->verifierAdmin();

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $objetDAO = new ObjetDAO($this->getPdo());

                $objet = new Objet(
                    null,
                    $_POST['description'],
                    $_POST['modele3dPath'],
                    (int) $_POST['prix']
                );

                $objetDAO->insererObjet($objet);
            }

            header('Location: index.php?controller=Admin&methode=dashboard');
            exit;
        }

        public function supprimerUtilisateur(): void {
            //$this->verifierAdmin();

            if (!isset($_GET['id'])) {
                header('Location: index.php?controller=Admin&methode=dashboard');
                exit;
            }

            $idUtilisateur = (int) $_GET['id'];
            $pdo = $this->getPdo();

            try {
                $pdo->beginTransaction();

                // Suppression de l'avatar de l'utilisateur
                $avatarDAO = new AvatarDAO($pdo);
                $avatar = $avatarDAO->supprimerAvatar(
                    $idUtilisateur
                );

                // Suppression des amis de l'utilisateur
                $amiDAO = new AmiDAO($pdo);
                $amis = $amiDAO->supprimerParUtilisateur($idUtilisateur);

                /*foreach ($amis as $ami) {
                    $amiDAO->supprimerAmi(
                        $ami->getIdUtilisateur1(),
                        $ami->getIdUtilisateur2()
                    );
                }*/

                // Supression de composer
                $composerDAO = new ComposerDAO($pdo);
                $composers = $composerDAO->findByIdUtilisateur($idUtilisateur);

                foreach ($composers as $composer) {
                    $composerDAO->supprimerComposer(
                        $composer->getIdGroupe(),
                        $idUtilisateur
                    );
                }

                // Suppression des messages de l'utilisateur
                $messageDAO = new MessageDAO($pdo);
                $messages = $messageDAO->findByUtilisateur($idUtilisateur);

                foreach ($messages as $message) {
                    $messageDAO->supprimerMessage(
                        $idUtilisateur
                    );
                }

                // Suppression des ajouts
                $ajouterDAO = new AjouterDAO($pdo);
                $ajouts = $ajouterDAO->findByIdUtilisateur($idUtilisateur);

                foreach ($ajouts as $ajout) {
                    $ajouterDAO->supprimerAjouter(
                        $ajout->getIdObjet(),
                        $idUtilisateur
                    );
                }

                // Suppression des achats que l'utilisateur a réalisé
                $achatDAO = new AchatDAO($pdo);
                $achats = $achatDAO->findByUtilisateur($idUtilisateur);

                foreach ($achats as $achat) {
                    $achatDAO->supprimerAchat(
                        $achat->getIdObjet(),
                        $idUtilisateur
                    );
                }

                // Suppression de la/les rooms de l'utilisateur et de tous les objets qu'elles contenaient
                $roomDAO = new RoomDAO($pdo);
                $possederDAO = new PossederDAO($pdo);
                $rooms = $roomDAO->findByCreateur($idUtilisateur);

                foreach ($rooms as $room) {
                    $posseders = $possederDAO->findByRoom($room->getIdRoom());

                    foreach($posseders as $posseder) {
                        $possederDAO->supprimerPosseder(
                            $room->getIdRoom(),
                            $posseder->getIdObjet()
                        );
                        $roomDAO->supprimerRoomByCreateur(
                            $idUtilisateur
                        );
                    }
                }

                // Suppression du/des posts de l'utilisateur
                $postDAO = new PostDAO($pdo);
                $posts = $postDAO->findPostsByAuteur($idUtilisateur);

                foreach ($posts as $post) {
                    $postDAO->supprimerPost(
                        $post->getIdPost(),
                        $idUtilisateur
                    );
                }

                // Suppression des réponses de l'utilisateur
                $reponseDAO = new ReponseDAO($pdo);
                $reponses = $reponseDAO->findByAuteur($idUtilisateur);

                foreach ($reponses as $reponse) {
                    $reponseDAO->supprimerReponse(
                        $reponse->getId(),
                        $idUtilisateur
                    );
                }

                // Suppression signaler
                $signalerDAO = new SignalerDAO($pdo);
                $signalers = $signalerDAO->findByIdUtilisateur($idUtilisateur);

                foreach ($signalers as $signaler) {
                    $signalerDAO->supprimerSignaler(
                        $signaler['idSignalement'],
                        $idUtilisateur
                        // manque l'id du post
                    );
                }

                // Suppression de l’utilisateur
                $utilisateurDAO = new UtilisateurDAO($pdo);
                $utilisateurDAO->supprimerUtilisateur($idUtilisateur);

                $pdo->commit();

            }
            catch (PDOException $e) {
                $pdo->rollBack();
                throw $e;
            }

            header('Location: index.php?controller=Admin&methode=dashboard');
            exit;
        }
    }