<?php
/**
 * Classe Quiz
 * 
 * Cette classe représente un quiz, une spécialisation de Post.
 * Elle permet de créer un objet Quiz et de l'utiliser avec les propriétés idQuiz, titre, description, choixMultiples et l'id de la question qui lui est associée.
 * 
 * Exemple d'utilisation :
 * $quiz = new Quiz(1, 'Quiz1', 'Description du quiz', true, 10);
 * echo $quiz->getTitre(); // Affiche 'Quiz1'
 * 
 */
class Quiz
{
    // ATTRIBUTS
    // Identifiant unique du quiz
    private ?int $idQuiz;

    // Titre du quiz
    private string $titre;

    // Description du quiz, peut être nulle
    private ?string $description;

    // Indique si le quiz permet des choix multiples
    private bool $choixMultiples;

    // Identifiant de la question associée au quiz
    private int $idQuestion;

    // Identifiant du post associé au quiz
    private int $idPost;

    // CONSTRUCTEUR
    /**
     * Constructeur de la classe Quiz.
     * 
     * Ce constructeur initialise un objet Quiz avec les propriétés spécifiées.
     * 
     * @param ?int $idQuiz Identifiant unique du quiz.
     * @param string $titre Titre du quiz.
     * @param ?string $description Description du quiz (peut être nulle).
     * @param bool $choixMultiples Indique si le quiz permet des choix multiples.
     * @param int $idQuestion Identifiant de la question associée au quiz.
     * @param int $idPost Identifiant du post associé au quiz.
     */
    public function __construct(
        ?int $idQuiz,
        string $titre,
        ?string $description,
        bool $choixMultiples,
        int $idQuestion,
        int $idPost
    ) {
        $this->setIdQuiz($idQuiz);
        $this->setTitre($titre);
        $this->setDescription($description);
        $this->setChoixMultiples($choixMultiples);
        $this->setIdQuestion($idQuestion);
        $this->setIdPost($idPost);
    }

    // DESTRUCTEUR
    /**
     * Destructeur de la classe Quiz.
     * 
     * Ce destructeur est vide mais peut être utilisé pour libérer des ressources si nécessaire.
     */
    public function __destruct()
    {
        // Rien à nettoyer ici
    }

    //ENCAPSULATION
    //GETTERS

    /**
     * Récupère l'identifiant du quiz.
     * 
     * @return ?int Identifiant du quiz, ou null si non défini.
     */
    public function getIdQuiz(): ?int {
        return $this->idQuiz;
    }

    /**
     * Récupère le titre du quiz.
     * 
     * @return string Titre du quiz.
     */
    public function getTitre(): string {
        return $this->titre;
    }

    /**
     * Récupère la description du quiz.
     * 
     * @return ?string Description du quiz, ou null si non définie.
     */
    public function getDescription(): ?string {
        return $this->description;
    }

    /**
     * Indique si le quiz permet des choix multiples.
     * 
     * @return bool True si le quiz permet des choix multiples, sinon false.
     */
    public function getChoixMultiples(): bool {
        return $this->choixMultiples;
    }

    /**
     * Récupère l'identifiant de la question associée au quiz.
     * 
     * @return int Identifiant de la question.
     */
    public function getIdQuestion(): int {
        return $this->idQuestion;
    }

    /**
     * Récupère l'identifiant du post associé au quiz.
     * 
     * @return int Identifiant du post.
     */
    public function getIdPost(): int {
        return $this->idPost;
    }

    //SETTERS

    /**
     * Définit l'identifiant du quiz.
     * 
     * @param ?int $idQuiz Identifiant du quiz à définir.
     */
    public function setIdQuiz(?int $idQuiz): void {
        $this->idQuiz = $idQuiz;
    }

    /**
     * Définit le titre du quiz.
     * 
     * @param string $titre Titre du quiz à définir.
     */
    public function setTitre(string $titre): void {
        $this->titre = $titre;
    }

    /**
     * Définit la description du quiz.
     * 
     * @param ?string $description Description du quiz à définir.
     */
    public function setDescription(?string $description): void {
        $this->description = $description;
    }

    /**
     * Définit si le quiz permet des choix multiples.
     * 
     * @param bool $choixMultiples True si le quiz permet des choix multiples, sinon false.
     */
    public function setChoixMultiples(bool $choixMultiples): void {
        $this->choixMultiples = $choixMultiples;
    }

    /**
     * Définit l'identifiant de la question associée au quiz.
     * 
     * @param int $idQuestion Identifiant de la question à définir.
     */
    public function setIdQuestion(int $idQuestion): void {
        $this->idQuestion = $idQuestion;
    }

    /**
     * Définit l'identifiant du post associé au quiz.
     * 
     * @param int $idPost Identifiant du post à définir.
     */    public function setIdPost(int $idPost): void {
        $this->idPost = $idPost;
    }
}
