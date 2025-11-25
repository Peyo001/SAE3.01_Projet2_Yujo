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
        private ?int $idRoom;
        private ?string $nom;
        private ?string $visibilite;
        private ?string $dateCreation;
        private ?int $nbVisit;
        private ?int $idCreateur;   // incorrect il faut enlever et créer une classe Créer

        // attribut métier
        private ?array $objets = [];    // liste d'objets présents dans la room

        public function __construct(?int $idRoom, ?string $nom, ?string $visibilite, ?string $dateCreation, ?int $nbVisit, ?int $idCreateur) {
            $this->setIdRoom($idRoom);
            $this->setNom($nom);
            $this->setVisibilite($visibilite);
            $this->setDateCreation($dateCreation);
            $this->setNbVisit($nbVisit);
            $this->setIdCreateur($idCreateur);
        }


        // Getters
        public function getIdRoom(): ?int {
            return $this->idRoom;
        }

        public function getNom(): ?string {
            return $this->nom;
        }

        public function getVisibilite(): ?string {
            return $this->visibilite;
        }

        public function getDateCreation(): ?string {
            return $this->dateCreation;
        }

        public function getNbVisit(): ?int {
            return $this->nbVisit;
        }

        public function getIdCreateur(): ?int {
            return $this->idCreateur;
        }

        public function getObjets(): array {
            return $this->objets;
        }

        // Setters
        public function setIdRoom(?int $idRoom): void {
            $this->idRoom = $idRoom;
        }

        public function setNom(?string $nom): void {
            $this->nom = $nom;
        }

        public function setVisibilite(?string $visibilite): void {
            $this->visibilite = $visibilite;
        }

        public function setDateCreation(?string $dateCreation): void {
            $this->dateCreation = $dateCreation;
        }

        public function setNbVisit(?int $nbVisit): void {
            $this->nbVisit = $nbVisit;
        }

        public function setIdCreateur(?int $idCreateur): void {
            $this->idCreateur = $idCreateur;
        }

        public function setObjets(array $objets): void {
            $this->objets = $objets;
        }

        public function addObjet(Objet $objet): void {
            $this->objets[] = $objet;
        }
    }