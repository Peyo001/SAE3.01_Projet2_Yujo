<?php
    /**
     * Classe Room
     * 
     * Cette classe représente une room virtuelle où les utilisateurs peuvent interagir avec des objets 3D.
     * Elle permet de créer un objet Room et de l'utiliser avec les propriétés idRoom, nom, visibilite, dateCreation, nbVisit et idCreateur.
     * 
     * Exemple d'utilisation :
     * $room = new Room(1, 'MaRoom', 'Publique', '2024-01-01', 100, 42);
     * echo $room->getNom(); // Affiche 'MaRoom'
     * 
     */
    
    class Room {
        //ATTRIBUTS
        // Identifiant unique de la room
        private ?int $idRoom;

        // Nom de la room
        private ?string $nom;

        // Visibilité de la room (publique ou privée)
        private ?string $visibilite;

        // Date de création de la room
        private ?string $dateCreation;

        // Nombre de visites de la room
        private ?int $nbVisit;

        // Identifiant de l'utilisateur qui a créé la room (ce champ sera modifié)
        private ?int $idCreateur;   // incorrect il faut enlever et créer une classe Créer

        // attribut métier
        private ?array $objets = [];    // liste d'objets présents dans la room

        //CONSTRUCTEUR 
        /**
         * Constructeur de la classe Room.
         * 
         * Ce constructeur initialise un objet `Room` avec les valeurs spécifiées pour les propriétés.
         * 
         * @param ?int $idRoom Identifiant de la room (peut être nul si non défini).
         * @param ?string $nom Nom de la room (peut être nul si non défini).
         * @param ?string $visibilite Visibilité de la room (peut être nul si non défini).
         * @param ?string $dateCreation Date de création de la room (peut être nul si non défini).
         * @param ?int $nbVisit Nombre de visites de la room (peut être nul si non défini).
         * @param ?int $idCreateur Identifiant du créateur de la room (peut être nul si non défini).
         */
        public function __construct(?int $idRoom, ?string $nom, ?string $visibilite, ?string $dateCreation, ?int $nbVisit, ?int $idCreateur) {
            $this->setIdRoom($idRoom);
            $this->setNom($nom);
            $this->setVisibilite($visibilite);
            $this->setDateCreation($dateCreation);
            $this->setNbVisit($nbVisit);
            $this->setIdCreateur($idCreateur);
        }


        // Getters
        /**
         * Récupère l'identifiant de la room.
         * 
         * @return ?int L'identifiant de la room, ou null si non défini.
         */
        public function getIdRoom(): ?int {
            return $this->idRoom;
        }

        /**
         * Récupère le nom de la room.
         * 
         * @return ?string Le nom de la room, ou null si non défini.
         */
        public function getNom(): ?string {
            return $this->nom;
        }

        /**
         * Récupère la visibilité de la room.
         * 
         * @return ?string La visibilité de la room (publique ou privée), ou null si non définie.
         */
        public function getVisibilite(): ?string {
            return $this->visibilite;
        }

        /**
         * Récupère la date de création de la room.
         * 
         * @return ?string La date de création de la room, ou null si non définie.
         */
        public function getDateCreation(): ?string {
            return $this->dateCreation;
        }

        /**
         * Récupère le nombre de visites de la room.
         * 
         * @return ?int Le nombre de visites de la room, ou null si non défini.
         */
        public function getNbVisit(): ?int {
            return $this->nbVisit;
        }

        /**
         * Récupère l'identifiant du créateur de la room.
         * 
         * @return ?int L'identifiant du créateur, ou null si non défini.
         */
        public function getIdCreateur(): ?int {
            return $this->idCreateur;
        }

        /**
         * Récupère la liste des objets présents dans la room.
         * 
         * @return array La liste des objets présents dans la room.
         */
        public function getObjets(): array {
            return $this->objets;
        }

        // Setters
        /**
         * Définit l'identifiant de la room.
         * 
         * @param ?int $idRoom L'identifiant de la room à définir.
         */
        public function setIdRoom(?int $idRoom): void {
            $this->idRoom = $idRoom;
        }   


        /**
         * Définit le nom de la room.
         * 
         * @param ?string $nom Le nom de la room à définir.
         */
        public function setNom(?string $nom): void {
            $this->nom = $nom;
        }

        /**
         * Définit la visibilité de la room.
         * 
         * @param ?string $visibilite La visibilité de la room à définir.
         */
        public function setVisibilite(?string $visibilite): void {
            $this->visibilite = $visibilite;
        }

        /**
         * Définit la date de création de la room.
         * 
         * @param ?string $dateCreation La date de création de la room à définir.
         */
        public function setDateCreation(?string $dateCreation): void {
            $this->dateCreation = $dateCreation;
        }   

        /**
         * Définit le nombre de visites de la room.
         * 
         * @param ?int $nbVisit Le nombre de visites de la room à définir.
         */
        public function setNbVisit(?int $nbVisit): void {
            $this->nbVisit = $nbVisit;
        }

        /**
         * Définit l'identifiant du créateur de la room.
         * 
         * @param ?int $idCreateur L'identifiant du créateur à définir.
         */
        public function setIdCreateur(?int $idCreateur): void {
            $this->idCreateur = $idCreateur;
        }

        /**
         * Définit la liste des objets présents dans la room.
         * 
         * @param array $objets La liste des objets à définir.
         */
        public function setObjets(array $objets): void {
            $this->objets = $objets;
        }

        /**
         * Ajoute un objet à la room.
         * 
         * Cette méthode permet d'ajouter un objet à la liste des objets présents dans la room.
         * 
         * @param Objet $objet L'objet à ajouter à la room.
         */
        public function addObjet(Objet $objet): void {
            $this->objets[] = $objet;
        }
    }