<?php
    // Création de la classe Room
    
    class Room {
        private ?int $idRoom;
        private ?string $nom;
        private ?string $visibilite;
        private ?int $nbVisit = 0;
        private ?array $objets;


        public function __construct(?int $idRoom = null, ?string $nom = null, ?string $visibilite = null, ?int $nbVisit = null, ?array $objets = null) {
            $this->setIdRoom($idRoom);
            $this->setNom($nom);
            $this->setVisibilite($visibilite);
            $this->setNbVisit($nbVisit);
            $this->setObjets($objets);
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

        public function getNbVisit(): ?int {
            return $this->nbVisit;
        }

        public function getObjets(): ?array {
            if ($this->objets == null) {
                $db = Bd::getInstance();
                $pdo = $db->getConnection();
                $manager = new ObjetsDao($pdo);
                $this->objets = $manager->findObjetsByRoom($this->idRoom);
            }
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

        public function setNbVisit(?int $nbVisit): void {
            $this->nbVisit = $nbVisit;
        }

        public function setObjets(?array $objets): void {
            $this->objets = $objets;
        }
    }