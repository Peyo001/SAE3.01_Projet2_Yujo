<?php
    // Création de la classe Room
    
    class Room {
        private ?int $idRoom;
        private ?string $nom;
        private ?string $visibilite;
        private ?string $dateCreation;
        private ?int $nbVisit;
        private ?int $idCreateur;

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
    }