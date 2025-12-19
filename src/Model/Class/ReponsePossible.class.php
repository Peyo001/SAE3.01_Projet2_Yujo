<?php
    /**
     * Classe ReponsePossible
     * 
     * Cette classe représente une réponse possible à une question dans un système de quiz.
     * Elle permet de créer un objet ReponsePossible et de l'utiliser avec les propriétés idReponsePossible, libelle et estCorrecte.
     * 
     * Exemple d'utilisation :
     * $reponse = new ReponsePossible(1, '42', true);
     * echo $reponse->getLibelle(); // Affiche '42'
     */

    class ReponsePossible {
        // Identifiant unique de la réponse possible
        private ?int $idReponsePossible;
        // Libellé de la réponse possible
        private ?string $libelle;
        // Indique si la réponse est correcte
        private ?bool $estCorrecte;
    
        /**
         * Constructeur de la classe ReponsePossible.
         * 
         * Ce constructeur initialise un objet ReponsePossible avec les propriétés spécifiées.
         * 
         * @param ?int $idReponsePossible Identifiant de la réponse possible (peut être nul si non défini).
         * @param ?string $libelle Libellé de la réponse possible (peut être nul si non défini).
         * @param ?bool $estCorrecte Indique si la réponse est correcte (peut être nul si non défini).
         */
        public function __construct(?int $idReponsePossible, ?string $libelle, ?bool $estCorrecte) {
            $this->setIdReponsePossible($idReponsePossible);
            $this->setLibelle($libelle);
            $this->setEstCorrecte($estCorrecte);
        }

        // Getters

        /**
         * Récupère l'identifiant de la réponse possible.
         * 
         * @return ?int L'identifiant de la réponse possible, ou null si non défini.
         */
        public function getIdReponsePossible(): ?int {
            return $this->idReponsePossible;
        }

        /**
         * Récupère le libellé de la réponse possible.
         * 
         * @return ?string Le libellé de la réponse possible, ou null si non défini.
         */
        public function getLibelle(): ?string {
            return $this->libelle;
        }

        /**
         * Indique si la réponse est correcte.
         * 
         * @return ?bool True si la réponse est correcte, false sinon, ou null si non défini.
         */
        public function getEstCorrecte(): ?bool {
            return $this->estCorrecte;
        }

        // Setters

        /**
         * Définit l'identifiant de la réponse possible.
         * 
         * @param ?int $idReponsePossible L'identifiant de la réponse possible (peut être nul).
         */
        public function setIdReponsePossible(?int $idReponsePossible): void {
            $this->idReponsePossible = $idReponsePossible;
        }

        /**
         * Définit le libellé de la réponse possible.
         * 
         * @param ?string $libelle Le libellé de la réponse possible (peut être nul).
         */
        public function setLibelle(?string $libelle): void {
            $this->libelle = $libelle;
        }

        /**
         * Définit si la réponse est correcte.
         * 
         * @param ?bool $estCorrecte True si la réponse est correcte, false sinon (peut être nul).
         */
        public function setEstCorrecte(?bool $estCorrecte): void {
            $this->estCorrecte = $estCorrecte;
        }
    }