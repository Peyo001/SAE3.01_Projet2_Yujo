<?php
/**
 * Classe Post
 * 
 * Cette classe représente un post créé par un utilisateur dans une room.
 * Elle permet de créer un objet Post et de l'utiliser avec les propriétés idPost, contenu, typePost, visibilite, datePublication, idAuteur.
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

    // Visibilité du post (public/prive)
    private ?string $visibilite = null;

    // Date de publication du post
    private ?string $datePublication = null;

    // Identifiant de l'utilisateur ayant créé le post
    private ?int $idAuteur = null;


    //CONSTRUCTEUR
    /**
     * Constructeur de la classe Post.
     * 
     * Ce constructeur initialise un objet `Post` avec les valeurs spécifiées pour les propriétés.
     * 
     * @param ?int $idPost Identifiant du post (peut être nul si non défini).
     * @param ?string $contenu Contenu du post (peut être nul si non défini).
     * @param ?string $typePost Type du post (peut être nul si non défini).
     * @param ?string $visibilite Visibilité du post ('public' ou 'prive').
     * @param ?string $datePublication Date de publication du post (peut être nul si non défini).
     * @param ?int $idAuteur Identifiant de l'auteur du post (peut être nul si non défini).
     *  
     */
    public function __construct(?int $idPost, ?string $contenu, ?string $typePost, ?string $visibilite, ?string $datePublication, ?int $idAuteur) {
        $this->setIdPost($idPost);
        $this->setContenu($contenu);
        $this->setTypePost($typePost);
        $this->setVisibilite($visibilite ?? 'public');
        $this->setDatePublication($datePublication);
        $this->setIdAuteur($idAuteur);
    }

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
     * Récupère la visibilité du post.
     * 
     * @return ?string 'public' ou 'prive'
     */
    public function getVisibilite(): ?string { return $this->visibilite; }

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

    

    // Setters
    /**
     * Définit l'identifiant du post.
     * 
     * @param ?int $idPost L'identifiant du post à définir.
     */
    public function setIdPost(?int $idPost): void { $this->idPost = $idPost; }
    
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
     * Définit la visibilité du post.
     * 
     * @param string $visibilite 'public' ou 'prive'.
     */
    public function setVisibilite(string $visibilite): void { $this->visibilite = $visibilite; }

    /**
     * Définit la date de publication du post.
     * 
     * @param string $datePublication La date de publication à définir.
     */
    public function setDatePublication(string $datePublication): void { $this->datePublication = $datePublication; }


    /**
     * Définit l'identifiant de l'auteur du post.
     * 
     * @param int $idAuteur L'identifiant de l'auteur à définir.
     */
    public function setIdAuteur(int $idAuteur): void { $this->idAuteur = $idAuteur; }
}
