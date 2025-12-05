<?php
/**
 * Classe Post
 * 
 * Cette classe représente un post créé par un utilisateur dans une room.
 * Elle permet de créer un objet Post et de l'utiliser avec les propriétés idPost, contenu, typePost, datePublication, idAuteur et idRoom.
 * 
 * Exemple d'utilisation : 
 * $post = new Post();
 * $post->setContenu('Bonjour tout le monde !');
 * echo $post->getContenu(); // Affiche 'Bonjour tout le monde !'
 */
class Post
{
    // Identifiant unique du post
    private ?int $idPost = null;

    // Contenu du post
    private ?string $contenu = null;

    // Type du post (par exemple, texte, quiz, défi, etc.)
    private ?string $typePost = null;

    // Date de publication du post
    private ?string $datePublication = null;

    // Identifiant de l'utilisateur ayant créé le post
    private ?int $idAuteur = null;

    // Identifiant de la room où le post est publié
    private ?int $idRoom = null;


    //CONSTRUCTEUR
    /**
     * Constructeur de la classe Post.
     * 
     * Ce constructeur est vide et est principalement utilisé pour l'instanciation via PDO::FETCH_CLASS.
     */
    public function __construct() {} // constructeur vide pour PDO::FETCH_CLASS

    // Getters
    /**
     * Récupère l'identifiant du post.
     * 
     * @return ?int Identifiant du post, ou null si non défini.
     */
    public function getIdPost(): ?int { return $this->idPost; }

    /**
     * Récupère le contenu du post.
     * 
     * @return ?string Le contenu du post, ou null si non défini.
     */
    public function getContenu(): ?string { return $this->contenu; }

    /**
     * Récupère le type du post.
     * 
     * @return ?string Le type du post, ou null si non défini.
     */
    public function getTypePost(): ?string { return $this->typePost; }

    /**
     * Récupère la date de publication du post.
     * 
     * @return ?string La date de publication du post, ou null si non défini.
     */
    public function getDatePublication(): ?string { return $this->datePublication; }

    /**
     * Récupère l'identifiant de l'auteur du post.
     * 
     * @return ?int L'identifiant de l'auteur, ou null si non défini.
     */
    public function getIdAuteur(): ?int { return $this->idAuteur; }

    /**
     * Récupère l'identifiant de la room dans laquelle le post a été publié.
     * 
     * @return ?int L'identifiant de la room, ou null si non défini.
     */
    public function getIdRoom(): ?int { return $this->idRoom; }

    // Setters
    /**
     * Définit l'identifiant du post.
     * 
     * @param int $idPost L'identifiant du post à définir.
     */
    public function setIdPost(int $idPost): void { $this->idPost = $idPost; }
    
    /**
     * Définit le contenu du post.
     * 
     * @param string $contenu Le contenu du post à définir.
     */
    public function setContenu(string $contenu): void { $this->contenu = $contenu; }

    /**
     * Définit le type du post.
     * 
     * @param string $typePost Le type du post à définir.
     */
    public function setTypePost(string $typePost): void { $this->typePost = $typePost; }

    /**
     * Définit la date de publication du post.
     * 
     * @param string $datePublication La date de publication à définir.
     */
    public function setDatePublication(string $datePublication): void { $this->datePublication = $datePublication; }

    public function setIdAuteur(int $idAuteur): void { $this->idAuteur = $idAuteur; }

    public function setIdRoom(int $idRoom): void { $this->idRoom = $idRoom; }
}
