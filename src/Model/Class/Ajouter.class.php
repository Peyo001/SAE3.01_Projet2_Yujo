<?php 
/**
 * Classe Ajouter
 * 
 * Cette classe représente l'ajout d'un objet par un utilisateur.
 * Elle permet de creer un objet Ajouter et de l'utiliser avec les propriétés idObjet, idUtilisateur et dateAjout.
 * 
 * Exemple d'utilisation :
 * $ajouter = new Ajouter(1, 42, '2024-01-01');
 * echo $ajouter->getIdObjet(); // Affiche 1
 * 
 */
class Ajouter {
    // Propriété représentant l'identifiant de l'objet à ajouter.
    private int $idObjet;

    // Propriété représentant l'identifiant de l'utilisateur ajoutant l'objet.
    private int $idUtilisateur;

    // Propriété représentant la date d'ajout de l'objet.
    private string $dateAjout;

    /**
     * Constructeur de la classe Ajouter.
     * 
     * Ce constructeur initialise un objet Ajouter avec les valeurs spécifiées pour 
     * l'identifiant de l'objet et l'identifiant de l'utilisateur.
     * 
     * @param int $idObjet Identifiant de l'objet à ajouter.
     * @param int $idUtilisateur Identifiant de l'utilisateur ajoutant l'objet.
     * @param string $dateAjout Date d'ajout de l'objet.
     */
    public function __construct(int $idObjet, int $idUtilisateur, string $dateAjout) {
        $this->idObjet = $idObjet;
        $this->idUtilisateur = $idUtilisateur;
        $this->dateAjout = $dateAjout;
    }

    /**
     * Récupère l'identifiant de l'objet à ajouter.
     * 
     * @return int Identifiant de l'objet à ajouter.
     */
    public function getIdObjet(): int {
        return $this->idObjet;
    }

    /**
     * Définit l'identifiant de l'objet à ajouter.
     * 
     * @param int $idObjet L'identifiant de l'objet à définir.
     * @return void
     */
    public function setIdObjet(int $idObjet): void {
        $this->idObjet = $idObjet;
    }

    /**
     * Récupère l'identifiant de l'utilisateur ajoutant l'objet.
     * 
     * @return int Identifiant de l'utilisateur ajoutant l'objet.
     */
    public function getIdUtilisateur(): int {
        return $this->idUtilisateur;
    }

    /**
     * Définit l'identifiant de l'utilisateur ajoutant l'objet.
     * 
     * @param int $idUtilisateur L'identifiant de l'utilisateur à définir.
     * @return void
     */
    public function setIdUtilisateur(int $idUtilisateur): void {
        $this->idUtilisateur = $idUtilisateur;
    }

    /**
     * Récupère la date d'ajout de l'objet.
     * 
     * @return string Date d'ajout de l'objet.
     */
    public function getDateAjout(): string {
        return $this->dateAjout;
    }

    /**
     * Définit la date d'ajout de l'objet.
     * 
     * @param string $dateAjout La date d'ajout de l'objet à définir.
     * @return void
     */
    public function setDateAjout(string $dateAjout): void {
        $this->dateAjout = $dateAjout;
    }
}