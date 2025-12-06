<?php
/**
 * Classe Reponse
 * 
 * Cette classe représente une réponse à un post dans un forum ou un système de commentaires.
 * Elle permet de créer un objet Reponse et de l'utiliser avec les propriétés idReponse, dateReponse, contenu, idAuteur et idPost.
 * 
 * Exemple d'utilisation :
 * $reponse = new Reponse(1, '2024-01-01', 'Ceci est une réponse.', 42, 10);
 * echo $reponse->getContenu(); // Affiche 'Ceci est une réponse.'
 */
class Reponse
{   
    // Identifiant unique de la réponse
    private ?int $idReponse;

    // Date de publication de la réponse
    private ?string $dateReponse;

    // Contenu de la réponse
    private ?string $contenu;

    // Identifiant de l'auteur de la réponse
    private ?int $idAuteur;

    // Identifiant du post auquel cette réponse est associée
    private ?int $idPost;


    //CONSTRUCTEUR
    /**
     * Constructeur de la classe Reponse.
     * 
     * Ce constructeur initialise un objet `Reponse` avec les valeurs spécifiées pour l'id, la date, le contenu, l'auteur et le post.
     * 
     * @param ?int $idReponse Identifiant de la réponse (peut être nul si non défini).
     * @param ?string $dateReponse Date de la réponse (peut être nulle si non définie).
     * @param ?string $contenu Contenu de la réponse (peut être nul si non défini).
     * @param ?int $idAuteur Identifiant de l'auteur de la réponse (peut être nul si non défini).
     * @param ?int $idPost Identifiant du post auquel la réponse appartient (peut être nul si non défini).
     */
    public function __construct(?int $idReponse = null, ?string $dateReponse = null, ?string $contenu = null, ?int $idAuteur = null, ?int $idPost = null)
    {
        $this->setId($idReponse);
        $this->setDateReponse($dateReponse);
        $this->setContenu($contenu);
        $this->setIdAuteur($idAuteur);
        $this->setIdPost($idPost);
    }
    

    //GETTERS ET SETTERS
    /**
     * Getter pour l'identifiant de la réponse.
     * 
     * @return ?int L'identifiant de la réponse, ou null si non défini.
     */
    public function getId(): ?int {
        return $this->idReponse;
    }

    /**
     * Setter pour l'identifiant de la réponse.
     * 
     * @param ?int $id L'identifiant de la réponse à définir.
     */
    public function setId(?int $id): void {
        $this->idReponse = $id;
    }

    /**
     * Getter pour la date de la réponse.
     * 
     * @return ?string La date de la réponse, ou null si non définie.
     */
    public function getDateReponse(): ?string {
        return $this->dateReponse;
    }

    /**
     * Setter pour la date de la réponse.
     * 
     * @param ?string $dateReponse La date de la réponse à définir.
     */
    public function setDateReponse(?string $dateReponse): void {
        $this->dateReponse = $dateReponse;
    }

    /**
     * Getter pour le contenu de la réponse.
     * 
     * @return ?string Le contenu de la réponse, ou null si non défini.
     */
    public function getContenu(): ?string {
        return $this->contenu;
    }

    /**
     * Setter pour le contenu de la réponse.
     * 
     * @param ?string $contenu Le contenu de la réponse à définir.
     */
    public function setContenu(?string $contenu): void{
        $this->contenu = $contenu;
    }

    /**
     * Getter pour l'identifiant de l'auteur de la réponse.
     * 
     * @return ?int L'identifiant de l'auteur, ou null si non défini.
     */
    public function getIdAuteur(): ?int {
        return $this->idAuteur;
    }

    /**
     * Setter pour l'identifiant de l'auteur de la réponse.
     * 
     * @param ?int $idAuteur L'identifiant de l'auteur à définir.
     */
    public function setIdAuteur(?int $idAuteur): void {
        $this->idAuteur = $idAuteur;
    }

    /**
     * Getter pour l'identifiant du post auquel la réponse appartient.
     * 
     * @return ?int L'identifiant du post, ou null si non défini.
     */
    public function getIdPost(): ?int {
        return $this->idPost;
    }

    /**
     * Setter pour l'identifiant du post auquel la réponse appartient.
     * 
     * @param ?int $idPost L'identifiant du post à définir.
     */
    public function setIdPost(?int $idPost): void {
        $this->idPost = $idPost;
    }

    

    /**
     * Affiche les détails de la réponse.
     * 
     * Cette méthode affiche les informations concernant la réponse, telles que son ID, la date, le contenu, l'auteur et le post auquel elle appartient.
     */
    public function afficher()
    {
        echo "ID de la reponse : " . $this->getId() . "<br/>";
        echo "Date de la reponse : " . $this->getDateReponse() . "<br/>";
        echo "Contenu de la reponse : " . $this->getContenu() . "<br/>";
        echo "ID de l'auteur de la reponse : " . $this->getIdAuteur() . "<br/>";
        echo "ID du post : " . $this->getIdPost() . "<br/>";
    }
}

?>