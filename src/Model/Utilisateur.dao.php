<?php
class UtilisateurDao
{
    //ATTRIBUT
    private PDO $conn;

    //CONSTRUCTEUR
    public function __construct()
    {
        $this->conn = Database::getInstance()->getConnection();
    }

    //DESTRUCTEUR
    public function __destruct()
    {

    }

    //METHODES
    public function findUtilisateurById(int $id): ?Utilisateur
    {
        $stmt = $this->conn->prepare("SELECT idUtilisateur,pseudo,email,motDePasse,typeCompte,estPremium,dateInscription,yuPoints FROM UTILISATEUR WHERE idUtilisateur = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            return new Utilisateur(
                $row['idUtilisateur'],
                $row['pseudo'],
                $row['email'],
                $row['motDePasse'],
                $row['typeCompte'],
                (bool)$row['estPremium'],
                $row['dateInscription'],
                (int)$row['yuPoints']
            );
        }
        return null;
    }

    public function findAllUsers(): array
    {
        $stmt = $this->conn->prepare("SELECT idUtilisateur,pseudo,email,motDePasse,typeCompte,estPremium,dateInscription,yuPoints  FROM UTILISATEUR");
        $stmt->execute();
        $users = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $users[] = new Utilisateur(
                $row['idUtilisateur'],
                $row['pseudo'],
                $row['email'],
                $row['motDePasse'],
                $row['typeCompte'],
                (bool)$row['estPremium'],
                $row['dateInscription'],
                (int)$row['yuPoints']
            );
        }
        return $users;
    }

    public function createUser(Utilisateur $user): bool
    {
        $stmt = $this->conn->prepare("INSERT INTO UTILISATEUR (pseudo, email, motDePasse, typeCompte, estPremium, dateInscription, yuPoints) VALUES (:pseudo, :email, :motDePasse, :typeCompte, :estPremium, :dateInscription, :yuPoints)");
        $stmt->bindValue(':pseudo', $user->getPseudo(), PDO::PARAM_STR);
        $stmt->bindValue(':email', $user->getEmail(), PDO::PARAM_STR);
        $stmt->bindValue(':motDePasse', $user->getMotDePasse(), PDO::PARAM_STR);
        $stmt->bindValue(':typeCompte', $user->getTypeCompte(), PDO::PARAM_STR);
        $stmt->bindValue(':estPremium', $user->getEstPremium(), PDO::PARAM_BOOL);
        $stmt->bindValue(':dateInscription', $user->getDateInscription(), PDO::PARAM_STR);
        $stmt->bindValue(':yuPoints', $user->getYuPoints(), PDO::PARAM_INT);

        return $stmt->execute();
    }
    
    public function deleteUser(int $id): bool
    {
        $stmt = $this->conn->prepare("DELETE FROM UTILISATEUR WHERE idUtilisateur = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}

?>