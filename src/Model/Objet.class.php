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
        private ?int $idObjet;
        private ?string $description;
        private ?string $modele3dPath;
        private ?int $prix;

        private ?int $idRoom;   //clé étrangère vers Room

        public function __construct(?int $idObjet, ?string $description, ?string $modele3dPath, ?int $prix, ?int $idRoom) {
            $this->setIdObjet($idObjet);
            $this->setDescription($description);
            $this->setModele3dPath($modele3dPath);
            $this->setPrix($prix);
            $this->setIdRoom($idRoom);
        }


        // Getters
        public function getIdObjet(): ?int {
            return $this->idObjet;
        }

        public function getDescription(): ?string {
            return $this->description;
        }

        public function getModele3dPath(): ?string {
            return $this->modele3dPath;
        }

        public function getPrix(): ?int {
            return $this->prix;
        }

        public function getIdRoom(): ?int {
            return $this->idRoom;
        }


        // Setters
        public function setIdObjet(?int $idObjet): void {
            $this->idObjet = $idObjet;
        }

        public function setDescription(?string $description): void {
            $this->description = $description;
        }

        public function setModele3dPath(?string $modele3dPath): void {
            $this->modele3dPath = $modele3dPath;
        }

        public function setPrix(?int $prix): void {
            $this->prix = $prix;
        }

        public function setIdRoom(?int $idRoom): void {
            $this->idRoom = $idRoom;
        }
    }