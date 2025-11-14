<?php
class Groupe
{
    // ATTRIBUTS
    private int $idGroupe;
    private string $nomGroupe;
    private ?string $descriptionGroupe;
    private ?string $dateCreation;
    private array $membres = [];    

    // CONSTRUCTEUR
    public function __construct(
        int $idGroupe,
        string $nom,
        ?string $description,
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
    public function __destruct()
    {
        // Rien à nettoyer ici
    }

    //ENCAPSULATION
    //GETTERS
    public function getIdGroupe(): int
    {
        return $this->idGroupe;
    }

    public function getNomGroupe(): string
    {
        return $this->nomGroupe;
    }
    public function getDescriptionGroupe(): ?string
    {
        return $this->descriptionGroupe;
    }

    public function getDateCreation(): ?string
    {
        return $this->dateCreation;
    }

    public function getMembres(): array
    {
        return $this->membres;
    }

    //SETTERS
    public function setIdGroupe(int $idGroupe): void
    {
        $this->idGroupe = $idGroupe;
    }

    public function setNomGroupe(string $nomGroupe): void
    {
        $this->nomGroupe = $nomGroupe;
    }

    public function setDescriptionGroupe(?string $descriptionGroupe): void
    {
        $this->descriptionGroupe = $descriptionGroupe;
    }


    public function setDateCreation(?string $dateCreation): void
    {
        $this->dateCreation = $dateCreation;
    }

    public function setMembres(array $membres): void
    {
        $this->membres = $membres;
    }

    //METHODES

    // Ajoute un membre au groupe
    public function addMembre($membre): void
    {
        if ($this->estMembre($membre)) {
            return; // Le membre est déjà dans le groupe
        }
        $this->membres[] = $membre;
    }

    // Supprime un membre du groupe
    public function removeMembre($membre): void
    {
        $index = array_search($membre, $this->membres); // renvoie l'index du membre ou false s'il n'est pas trouvé
        if ($index !== false) {
            unset($this->membres[$index]); //enleve le membre du tableau si trouvé
            $this->membres = array_values($this->membres); // reindexe le tableau
        }
    }

    // Vérifie si un utilisateur est membre du groupe
    public function estMembre($membre): bool
    {
        return in_array($membre, $this->membres);
    }

    public function getListeMembres(): string 
    {
        return implode(", ", $this->membres);
    }


}