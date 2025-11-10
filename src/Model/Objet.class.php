<?php
    // Création de la classe Objet
    
    class Objet {
        private ?int $idObjet;
        private ?string $description;
        private ?string $modele3dPath;
        private ?int $prix;

        public function __construct(?int $idObjet, ?string $descritption, ?string $modele3dPath, ?int $prix) {
            $this->setIdObjet($idObjet);
            $this->setDescription($description);
            $this->setModele3dPath($modele3dPath);
            $this->setPrix($prix);
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

        public function setPrix(?string $prix): void {
            $this->prix = $prix;
        }
    }