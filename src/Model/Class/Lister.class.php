<?php
/**
 * Classe Lister
 * 
 * Cette classe représente la relation d'association entre une question et une réponse
 * Elle permet de créer un objet Lister et de l'utiliser avec les propriétés idReponsePossible et idQuestion.
 * 
 * Exemple d'utilisation :
 * $lister = new Lister(1, 42);
 * echo $lister->getIdReponsePossible(); // Affiche 1
 * 
 */
class Lister {
    private int $idReponsePossible;
    private int $idQuestion;

     /**
     * Constructeur de la classe Lister.
     * 
     * @param int $idReponsePossible Identifiant de la réponse.
     * @param int $idQuestion Identifiant de la question.
     */

    public function __construct(int $idReponsePossible, int $idQuestion) {
        $this->idReponsePossible = $idReponsePossible;
        $this->idQuestion = $idQuestion;
    }

    //ENCAPSULATION 
    /**
     * Récupère l'identifiant de la réponse.
     * @return int Identifiant de la réponse.
     * 
    */
    public function getIdReponsePossible(): int {
        return $this->idReponsePossible;
    }

    /**
     * Définit l'identifiant de la réponse.
     * @param int $idReponsePossible Identifiant de la réponse à définir.
     */
    public function setIdReponsePossible(int $idReponsePossible): void {
        $this->idReponsePossible = $idReponsePossible;
    }

    /**
     * Récupère l'identifiant de la question.
     * @return int Identifiant de la question.
     * 
    */
    public function getIdQuestion(): int {
        return $this->idQuestion;
    }

    /**
     * Définit l'identifiant de la question.
     * @param int $idQuestion Identifiant de la question à définir.
     * 
    */
    public function setIdQuestion(int $idQuestion): void {
        $this->idQuestion = $idQuestion;
    }
}