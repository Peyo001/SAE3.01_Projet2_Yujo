<?php
    /**
     * Classe Objet
     * 
     * Cette classe représente un objet 3D disponible dans une room.
     * Elle permet de créer un objet Objet et de l'utiliser avec les propriétés idObjet, description, modele3dPath, prix et idRoom.
     * 
     * Exemple d'utilisation :
     * $objet = new Objet(1, 'Chaise en bois', '/models/chaise.obj', 100, 5);
     * echo $objet->getDescription(); // Affiche 'Chaise en bois'
     */
    
    class Objet {
        // Identifiant unique de l'objet
        private ?int $idObjet;
        // Description de l'objet
        private ?string $description;
        // Chemin vers le fichier 3D de l'objet
        private ?string $modele3dPath;
        // Prix de l'objet
        private ?int $prix;
        // Identifiant de la room associée (optionnel)
        private ?int $idRoom;
    
        /**
         * Constructeur de la classe Objet.
         * 
         * Ce constructeur initialise un objet Objet avec les propriétés spécifiées.
         * 
         * @param ?int $idObjet Identifiant de l'objet (peut être nul si non défini).
         * @param ?string $description Description de l'objet (peut être nulle si non définie).
         * @param ?string $modele3dPath Chemin vers le modèle 3D de l'objet (peut être nul si non défini).
         * @param ?int $prix Prix de l'objet (peut être nul si non défini).
         * @param ?int $idRoom Identifiant de la room dans laquelle l'objet est disponible (peut être nul si non défini).
         */
        public function __construct(?int $idObjet, ?string $description, ?string $modele3dPath, ?int $prix, ?int $idRoom) {
            $this->setIdObjet($idObjet);
            $this->setDescription($description);
            $this->setModele3dPath($modele3dPath);
            $this->setPrix($prix);
            $this->setIdRoom($idRoom);
        }


        // Getters

        /**
         * Récupère l'identifiant de l'objet.
         * 
         * @return ?int L'identifiant de l'objet, ou null si non défini.
         */
        public function getIdObjet(): ?int {
            return $this->idObjet;
        }

        /**
         * Récupère l'identifiant de l'objet.
         * 
         * @return ?int L'identifiant de l'objet, ou null si non défini.
         */
        public function getDescription(): ?string {
            return $this->description;
        }

        /**
         * Récupère la description de l'objet.
         * 
         * @return ?string La description de l'objet, ou null si non définie.
         */
        public function getModele3dPath(): ?string {
            return $this->modele3dPath;
        }

        /**
         * Récupère le prix de l'objet.
         * 
         * @return ?int Le prix de l'objet, ou null si non défini.
         */
        public function getPrix(): ?int {
            return $this->prix;
        }
        /**
         * Récupère l'identifiant de la room associée.
         * 
         * @return ?int L'identifiant de la room, ou null si non défini.
         */
        public function getIdRoom(): ?int {
            return $this->idRoom;
        }
        // Setters

        /**
         * Récupère l'identifiant de la room dans laquelle l'objet est disponible.
         * 
         * @return ?int L'identifiant de la room, ou null si non défini.
         */
        public function setIdObjet(?int $idObjet): void {
            $this->idObjet = $idObjet;
        }

        /**
         * Définit la description de l'objet.
         * 
         * @param ?string $description La description de l'objet à définir.
         */
        public function setDescription(?string $description): void {
            $this->description = $description;
        }

        /**
         * Définit le chemin vers le modèle 3D de l'objet.
         * 
         * @param ?string $modele3dPath Le chemin du fichier modèle 3D à définir.
         */
        public function setModele3dPath(?string $modele3dPath): void {
            $this->modele3dPath = $modele3dPath;
        }

        /**
         * Définit le prix de l'objet.
         * 
         * @param ?int $prix Le prix de l'objet à définir.
         */
        public function setPrix(?int $prix): void {
            $this->prix = $prix;
        }   

        /**
         * Définit l'identifiant de la room associée.
         * 
         * @param ?int $idRoom L'identifiant de la room à définir.
         */
        public function setIdRoom(?int $idRoom): void {
            $this->idRoom = $idRoom;
        }

    }