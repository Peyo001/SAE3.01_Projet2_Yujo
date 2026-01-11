<?php
/**
 * Classe NewsletterDAO
 * 
 * Cette classe gère l'accès aux données de la table Newsletter.
 * 
 * Exemple d'utilisation :
 * $dao = new NewsletterDAO($pdo);
 * $dao->inscrire('user@example.com');
 */
class NewsletterDAO extends Dao
{
    /**
     * Inscrit un email à la newsletter.
     * 
     * @param string $email Email à inscrire.
     * @return bool True si l'inscription réussit, false sinon.
     */
    public function inscrire(string $email): bool
    {
        // Réactive si déjà présent mais désinscrit
        $query = "INSERT INTO NEWSLETTER (email, dateInscription, estActif) 
                  VALUES (:email, NOW(), TRUE)
                  ON DUPLICATE KEY UPDATE estActif = TRUE, dateInscription = NOW()";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        return $stmt->execute();
    }

    /**
     * Vérifie si un email est déjà inscrit.
     * 
     * @param string $email Email à vérifier.
     * @return bool True si l'email existe, false sinon.
     */
    public function emailExiste(string $email): bool
    {
        // Ne compte que les inscriptions actives
        $query = "SELECT COUNT(*) FROM NEWSLETTER WHERE email = :email AND estActif = TRUE";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Désactive une inscription à la newsletter.
     * 
     * @param string $email Email à désinscrire.
     * @return bool True si la désinscription réussit, false sinon.
     */
    public function desinscrire(string $email): bool
    {
        $query = "UPDATE NEWSLETTER SET estActif = FALSE WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        return $stmt->execute();
    }

    /**
     * Récupère tous les emails actifs inscrits à la newsletter.
     * 
     * @return array Tableau d'objets Newsletter.
     */
    public function getInscritsActifs(): array
    {
        $query = "SELECT * FROM NEWSLETTER WHERE estActif = TRUE ORDER BY dateInscription DESC";
        $stmt = $this->conn->query($query);
        $resultats = [];
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $resultats[] = new Newsletter(
                $row['email'],
                $row['dateInscription'],
                (bool)$row['estActif'],
                $row['idNewsletter']
            );
        }
        
        return $resultats;
    }

    /**
     * Compte le nombre total d'inscrits actifs.
     * 
     * @return int Nombre d'inscrits actifs.
     */
    public function compterInscritsActifs(): int
    {
        $query = "SELECT COUNT(*) FROM NEWSLETTER WHERE estActif = TRUE";
        $stmt = $this->conn->query($query);
        return (int)$stmt->fetchColumn();
    }
}
