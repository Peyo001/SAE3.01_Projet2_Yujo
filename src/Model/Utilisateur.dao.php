<?php
/**
 * 
 * Classe UtilisateurDao
 * 
 * Cette classe gère les opérations CRUD pour les utilisateurs dans la base de données.
 * Elle utilise la classe Database pour obtenir une connexion PDO.
 * 
 * Exemple d'utilisation :
 * $utilisteurDao = new UtilisateurDao();
 * $utilisateur = $utilisateurDao->findAll();
 */
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
    public function find(int $id): ?Utilisateur
    {
        $stmt = $this->conn->prepare("SELECT idUtilisateur, nom, prenom, genre, dateNaissance, pseudo, email, motDePasse, typeCompte, estPremium, dateInscription, yuPoints, personnalisation FROM UTILISATEUR WHERE idUtilisateur = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            return new Utilisateur(
                $row['idUtilisateur'],
                $row['nom'],
                $row['prenom'],
                $row['dateNaissance'],
                $row['genre'],
                $row['pseudo'],
                $row['email'],
                $row['motDePasse'],
                $row['typeCompte'],
                (bool)$row['estPremium'],
                $row['dateInscription'],
                (int)$row['yuPoints'],
                $row['personnalisation']
            );
        }
        return null;
    }

    public function findAll(): array
    {
        $stmt = $this->conn->prepare("SELECT idUtilisateur,nom,prenom,genre,dateNaissance,pseudo,email,motDePasse,typeCompte,estPremium,dateInscription,yuPoints FROM UTILISATEUR");
        $stmt->execute();
        $users = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $users[] = new Utilisateur(
                $row['idUtilisateur'],
                $row['nom'],
                $row['prenom'],
                $row['dateNaissance'],
                $row['genre'],
                $row['pseudo'],
                $row['email'],
                $row['motDePasse'],
                $row['typeCompte'],
                (bool)$row['estPremium'],
                $row['dateInscription'],
                (int)$row['yuPoints'],
                $row['personnalisation'],
            );
        }
        return $users;
    }

    public function createUser(Utilisateur $user): bool
    {
        $stmt = $this->conn->prepare("INSERT INTO UTILISATEUR (nom, prenom, dateNaissance, genre, pseudo, email, motDePasse, typeCompte, estPremium, dateInscription, yuPoints, personnalisation) VALUES (:nom, :prenom, :dateNaissance, :genre, :pseudo, :email, :motDePasse, :typeCompte, :estPremium, :dateInscription, :yuPoints, :personnalisation)");
        $stmt->bindValue(':nom', $user->getNom(), PDO::PARAM_STR);
        $stmt->bindValue(':prenom', $user->getPrenom(), PDO::PARAM_STR);
        $stmt->bindValue(':dateNaissance', $user->getDateNaiss(), PDO::PARAM_STR);
        $stmt->bindValue(':genre', $user->getGenre(), PDO::PARAM_STR);
        $stmt->bindValue(':pseudo', $user->getPseudo(), PDO::PARAM_STR);
        $stmt->bindValue(':email', $user->getEmail(), PDO::PARAM_STR);
        $stmt->bindValue(':motDePasse', $user->getMotDePasse(), PDO::PARAM_STR);
        $stmt->bindValue(':typeCompte', $user->getTypeCompte(), PDO::PARAM_STR);
        $stmt->bindValue(':estPremium', $user->getEstPremium(), PDO::PARAM_BOOL);
        $stmt->bindValue(':dateInscription', $user->getDateInscription(), PDO::PARAM_STR);
        $stmt->bindValue(':yuPoints', $user->getYuPoints(), PDO::PARAM_INT);
        $stmt->bindValue(':personnalisation', $user->getPersonnalisation(), PDO::PARAM_STR);
        return $stmt->execute();
    }
    
    public function deleteUser(int $id): bool
    {
        $stmt = $this->conn->prepare("DELETE FROM UTILISATEUR WHERE idUtilisateur = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}

?>