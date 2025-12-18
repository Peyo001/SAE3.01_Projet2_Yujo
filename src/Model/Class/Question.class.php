<?php
/**
 * Classe Question
 * 
 * Cette classe représente une question utilisée dans un quiz.
 * Elle permet de créer un objet Question et de l'utiliser avec les propriétés idQuestion, libelle est reponses.
 * 
 * Exemple d'utilisation :
 * $question = new Question(1, 'Quelle est la capitale de la France?', ['Paris', 'Londres', 'Berlin', 'Madrid']);
 * echo $question->getLibelle(); // Affiche 'Quelle est la capitale de la France?'
 * 
 */
class Question
{
    // ATTRIBUTS
    // Identifiant unique de la question
    private ?int $idQuestion;

    // Libellé de la question
    private string $libelle;

    // Réponses possibles à la question
    private array $reponses = [];

    // CONSTRUCTEUR
    /**
     * Constructeur de la classe Question.
     * 
     * Ce constructeur initialise un objet Question avec les propriétés spécifiées.
     *
     * @param ?int $idQuestion Identifiant unique de la question.
     * @param string $libelle Libellé de la question.
     * @param array $reponses Liste des réponses possibles à la question.
     */
    public function __construct(?int $idQuestion, string $libelle, array $reponses) {
        $this->setIdQuestion($idQuestion);
        $this->setLibelle($libelle);
        $this->setReponses($reponses);
    }

    // DESTRUCTEUR
    /**
     * Destructeur de la classe Question.
     * 
     * Ce destructeur est vide mais peut être utilisé pour libérer des ressources si nécessaire.
     */
    public function __destruct() {
        // Rien à nettoyer ici
    }

    //ENCAPSULATION
    //GETTERS

    /**
     * Récupère l'identifiant de la question.
     * 
     * @return ?int Identifiant de la question, ou null si non défini.
     */    public function getIdQuestion(): ?int {
        return $this->idQuestion;
    }

    /**
     * Récupère le libellé de la question.
     * 
     * @return string Libellé de la question.
     */
    public function getLibelle(): string {
        return $this->libelle;
    }

    /**
     * Récupère les réponses possibles à la question.
     * 
     * @return array Liste des réponses possibles.
     */
    public function getReponses(): array {
        return $this->reponses;
    }

    //SETTERS

    /**
     * Définit l'identifiant de la question.
     * 
     * @param ?int $idQuestion Identifiant de la question à définir.
     */
    public function setIdQuestion(?int $idQuestion): void {
        $this->idQuestion = $idQuestion;
    }

    /**
     * Définit le libellé de la question.
     * 
     * @param string $libelle Libellé de la question à définir.
     */
    public function setLibelle(string $libelle): void {
        $this->libelle = $libelle;
    }

    /**
     * Définit les réponses possibles à la question.
     * 
     * @param array $reponses Liste des réponses possibles à définir.
     */
    public function setReponses(array $reponses): void {
        $this->reponses = $reponses;
    }
}
