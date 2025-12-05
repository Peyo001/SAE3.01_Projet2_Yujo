<?php
/**
 * Classe Groupe
 * 
 * Cette classe représente un groupe d'utilisateurs.
 * Elle permet de créer un objet Groupe et de l'utiliser avec les propriétés idGroupe, nomGroupe, description, dateCreationGroupe et membres.
 * 
 * Exemple d'utilisation :
 * $groupe = new Groupe(1, 'Groupe1', 'Description du groupe', '2024-01-01', ['Alice', 'Bob']);
 * echo $groupe->getListeMembres(); // Affiche "Alice, Bob"
 * 
 */
class Groupe
{
    // ATTRIBUTS
    // Identifiant unique du groupe
    private int $idGroupe;

    // Nom du groupe
    private string $nomGroupe;

    // Description du groupe, peut être nulle
    private ?string $description;

    // Date de création du groupe, peut être nulle
    private ?string $dateCreationGroupe;

    // Liste des membres du groupe, sous forme de tableau
    private array $membres = [];    

    // CONSTRUCTEUR
    /**
     * Constructeur de la classe Groupe.
     * 
     * Ce constructeur initialise un objet Groupe avec les propriétés spécifiées.
     * 
     * @param int $idGroupe Identifiant unique du groupe.
     * @param string $nomGroupe Nom du groupe.
     * @param ?string $descriptionGroupe Description du groupe (peut être nulle).
     * @param ?string $dateCreation Date de création du groupe (peut être nulle).
     * @param array $membres Liste des membres du groupe (par défaut un tableau vide).
     */
    public function __construct(
        ?int $idGroupe = null,
        string $nomGroupe,
        ?string $descriptionGroupe,
        ?string $dateCreation,
        array $membres = []                                                                                                             
    ) {
        $this->setIdGroupe($idGroupe);
        $this->setNomGroupe($nomGroupe);
        $this->setDescriptionGroupe($descriptionGroupe);
        $this->setDateCreation($dateCreation);
        $this->setMembres($membres);
    }


    // DESTRUCTEUR
    /**
     * Destructeur de la classe Groupe.
     * 
     * Ce destructeur est vide mais peut être utilisé pour nettoyer des ressources si nécessaire.
     */
    public function __destruct()
    {
        // Rien à nettoyer ici
    }

    //ENCAPSULATION
    //GETTERS

    /**
     * Récupère l'identifiant du groupe.
     * 
     * @return int Identifiant du groupe.
     */
    public function getIdGroupe(): int
    {
        return $this->idGroupe;
    }


    /**
     * Récupère le nom du groupe.
     * 
     * @return string Nom du groupe.
     */
    public function getNomGroupe(): string
    {
        return $this->nomGroupe;
    }

    /**
     * Récupère la description du groupe.
     * 
     * @return ?string Description du groupe, ou null si non spécifiée.
     */
    public function getDescriptionGroupe(): ?string
    {
        return $this->description;
    }


    /**
     * Récupère la date de création du groupe.
     * 
     * @return ?string Date de création du groupe, ou null si non spécifiée.
     */
    public function getDateCreation(): ?string
    {
        return $this->dateCreationGroupe;
    }   

    /**
     * Récupère la liste des membres du groupe.
     * 
     * @return array Liste des membres du groupe.
     */
    public function getMembres(): array
    {
        return $this->membres;
    }

    //SETTERS

    /**
     * Définit l'identifiant du groupe.
     * 
     * @param int $idGroupe L'identifiant du groupe à définir.
     */
    public function setIdGroupe(int $idGroupe): void
    {
        $this->idGroupe = $idGroupe;
    }

    /**
     * Définit le nom du groupe.
     * 
     * @param string $nomGroupe Le nom du groupe à définir.
     */
    public function setNomGroupe(string $nomGroupe): void
    {
        $this->nomGroupe = $nomGroupe;
    }

    /**
     * Définit la description du groupe.
     * 
     * @param ?string $descriptionGroupe La description du groupe à définir (peut être nulle).
     */
    public function setDescriptionGroupe(?string $descriptionGroupe): void
    {
        $this->description = $descriptionGroupe;
    }

    /**
     * Définit la date de création du groupe.
     * 
     * @param ?string $dateCreation La date de création du groupe à définir (peut être nulle).
     */
    public function setDateCreation(?string $dateCreation): void
    {
        $this->dateCreationGroupe = $dateCreation;
    }

    /**
     * Définit la liste des membres du groupe.
     * 
     * @param array $membres La liste des membres à définir.
     */
    public function setMembres(array $membres): void
    {
        $this->membres = $membres;
    }

    //METHODES

     /**
     * Ajoute un membre au groupe.
     * 
     * Cette méthode permet d'ajouter un membre à la liste des membres du groupe, si ce membre n'est pas déjà présent.
     * 
     * @param string $membre Le membre à ajouter.
     */
    public function addMembre($membre): void
    {
        if ($this->estMembre($membre)) {
            return; // Le membre est déjà dans le groupe
        }
        $this->membres[] = $membre;
    }

    
     /**
     * Supprime un membre du groupe.
     * 
     * Cette méthode permet de supprimer un membre du groupe, si celui-ci fait partie du groupe.
     * 
     * @param string $membre Le membre à supprimer.
     */
    public function removeMembre($membre): void
    {
        $index = array_search($membre, $this->membres); // renvoie l'index du membre ou false s'il n'est pas trouvé
        if ($index !== false) {
            unset($this->membres[$index]); //enleve le membre du tableau si trouvé
            $this->membres = array_values($this->membres); // reindexe le tableau
        }
    }

    /**
     * Vérifie si un utilisateur est membre du groupe.
     * 
     * Cette méthode permet de vérifier si un membre fait partie du groupe.
     * 
     * @param string $membre Le membre à vérifier.
     * @return bool Retourne true si le membre est dans le groupe, sinon false.
     */
    public function estMembre($membre): bool
    {
        return in_array($membre, $this->membres);
    }

    /**
     * Récupère la liste des membres sous forme de chaîne de caractères.
     * 
     * Cette méthode convertit la liste des membres en une chaîne de caractères, séparée par des virgules.
     * 
     * @return string Liste des membres sous forme de chaîne de caractères.
     */
    public function getListeMembres(): string 
    {
        return implode(", ", $this->membres);
    }


}