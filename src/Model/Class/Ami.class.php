<?php 
/**
 * Classe Ami
 * 
 * Cette classe permet de lier deux utilisateur.
 * Elle permet de créer un objet Ami et de l'utiliser avec les propriétés idUtilisateur1, idUtilisateur2 et dateAjout.
 * 
 * Exemple d'utilisation :
 * $ami = new Ami(1, 2, '2024-01-01');
 * echo $ami->getIdUtilisateur1(); // Affiche l'ami 1
 * 
 */
class Ami {
    // Propriété représentant l'identifiant du premier utilisateur.
    private int $idUtilisateur1;

    // Propriété représentant l'identifiant du deuxième utilisateur.
    private int $idUtilisateur2;

    // Propriété représentant la date à laquelle l'ami a été ajouté. Elle peut être nulle si non spécifiée.
    private ?string $dateAjout;

    /**
     * Constructeur de la classe Ami.
     * 
     * Ce constructeur initialise un objet Ami avec les identifiants des deux utilisateurs et la date d'ajout de l'ami.
     * 
     * @param int $idUtilisateur1 Identifiant du premier utilisateur.
     * @param int $idUtilisateur2 Identifiant du deuxième utilisateur.
     * @param ?string $dateAjout Date d'ajout de l'ami (peut être nulle).
     */
    public function __construct(int $idUtilisateur1, int $idUtilisateur2, ?string $dateAjout) {
        $this->idUtilisateur1 = $idUtilisateur1;
        $this->idUtilisateur2 = $idUtilisateur2;
        $this->dateAjout = $dateAjout;
    }


    /**
     * Récupère l'identifiant du premier utilisateur.
     * 
     * @return int Identifiant du premier utilisateur.
     */
    public function getIdUtilisateur1(): int {
        return $this->idUtilisateur1;
    }

    /**
     * Définit l'identifiant du premier utilisateur.
     * 
     * @param int $idUtilisateur1 L'identifiant du premier utilisateur à définir.
     * @return void
     */
    public function setIdUtilisateur1(int $idUtilisateur1): void {
        $this->idUtilisateur1 = $idUtilisateur1;
    }

    /**
     * Récupère l'identifiant du deuxième utilisateur.
     * 
     * @return int Identifiant du deuxième utilisateur.
     */
    public function getIdUtilisateur2(): int {
        return $this->idUtilisateur2;
    }

    /**
     * Définit l'identifiant du deuxième utilisateur.
     * 
     * @param int $idUtilisateur2 L'identifiant du deuxième utilisateur à définir.
     * @return void
     */
    public function setIdUtilisateur2(int $idUtilisateur2): void {
        $this->idUtilisateur2 = $idUtilisateur2;
    }

    /**
     * Récupère la date à laquelle l'ami a été ajouté.
     * 
     * @return ?string La date d'ajout de l'ami, ou null si non spécifiée.
     */
    public function getDateAjout(): ?string {
        return $this->dateAjout;
    }

    /**
     * Définit la date à laquelle l'ami a été ajouté.
     * 
     * @param ?string $dateAjout La date d'ajout de l'ami à définir. Peut être nulle.
     * @return void
     */
    public function setDateAjout(?string $dateAjout): void {
        $this->dateAjout = $dateAjout;
    }
}