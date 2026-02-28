<?php
require_once "Dao.class.php";

/**
 * Classe InvitationDao
 * 
 * Cette classe gère les opérations de base de données pour les invitations de groupes (table INVITER).
 * Elle utilise la classe Database pour obtenir une connexion PDO.
 * 
 * Exemple d'utilisation :
 * $invitationDao = new InvitationDao($pdo);
 * $invitations = $invitationDao->findByInvite(5);
 */
class InvitationDao extends Dao
{
    /**
     * Hydrate une ligne de résultat en un objet Invitation.
     * 
     * @param array $row Ligne de résultat de la base de données.
     * @return Invitation Retourne un objet Invitation
     */
    public function hydrate(array $row): Invitation
    {
        return new Invitation(
            (int)$row['idHote'],
            (int)$row['idInvite'],
            (int)$row['idGroupe'],
            $row['dateInvitation'],
            $row['statut'] ?? 'en_attente'
        );
    }

    /**
     * Récupère toutes les invitations en attente pour un utilisateur invité.
     * 
     * @param int $idInvite L'identifiant de l'utilisateur invité.
     * @return Invitation[] Tableau des invitations en attente.
     */
    public function findByInvite(int $idInvite): array
    {
        $stmt = $this->conn->prepare("
            SELECT idHote, idInvite, idGroupe, dateInvitation, statut 
            FROM inviter
            WHERE idInvite = :idInvite AND statut = 'en_attente'
            ORDER BY dateInvitation DESC
        ");
        $stmt->bindValue(':idInvite', $idInvite, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $this->hydrateAll($rows);
    }

    /**
     * Récupère toutes les invitations pour un groupe.
     * 
     * @param int $idGroupe L'identifiant du groupe.
     * @return Invitation[] Tableau de toutes les invitations du groupe.
     */
    public function findByGroupe(int $idGroupe): array
    {
        $stmt = $this->conn->prepare("
            SELECT idHote, idInvite, idGroupe, dateInvitation, statut 
            FROM inviter
            WHERE idGroupe = :idGroupe
            ORDER BY dateInvitation DESC
        ");
        $stmt->bindValue(':idGroupe', $idGroupe, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $this->hydrateAll($rows);
    }

    /**
     * Recherche une invitation spécifique par ses clés composites.
     * Retourne l'invitation la plus récente en attente si plusieurs existent.
     * 
     * @param int $idHote L'identifiant de l'utilisateur hôte.
     * @param int $idInvite L'identifiant de l'utilisateur invité.
     * @param int $idGroupe L'identifiant du groupe.
     * @return Invitation|null Retourne l'invitation ou null si non trouvée.
     */
    public function find(int $idHote, int $idInvite, int $idGroupe): ?Invitation
    {
        $stmt = $this->conn->prepare("
            SELECT idHote, idInvite, idGroupe, dateInvitation, statut 
            FROM inviter
            WHERE idHote = :idHote AND idInvite = :idInvite AND idGroupe = :idGroupe
            ORDER BY dateInvitation DESC
            LIMIT 1
        ");
        $stmt->bindValue(':idHote', $idHote, PDO::PARAM_INT);
        $stmt->bindValue(':idInvite', $idInvite, PDO::PARAM_INT);
        $stmt->bindValue(':idGroupe', $idGroupe, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? $this->hydrate($row) : null;
    }

    /**
     * Crée une nouvelle invitation.
     * 
     * @param Invitation $invitation L'objet Invitation à créer.
     * @return bool true si la création a réussi, false sinon.
     */
    public function creerInvitation(Invitation $invitation): bool
    {
        $stmt = $this->conn->prepare("
            INSERT INTO inviter (idHote, idInvite, idGroupe, dateInvitation, statut)
            VALUES (:idHote, :idInvite, :idGroupe, :dateInvitation, :statut)
        ");
        $stmt->bindValue(':idHote', $invitation->getIdHote(), PDO::PARAM_INT);
        $stmt->bindValue(':idInvite', $invitation->getIdInvite(), PDO::PARAM_INT);
        $stmt->bindValue(':idGroupe', $invitation->getIdGroupe(), PDO::PARAM_INT);
        $stmt->bindValue(':dateInvitation', $invitation->getDateInvitation(), PDO::PARAM_STR);
        $stmt->bindValue(':statut', $invitation->getStatut(), PDO::PARAM_STR);

        return $stmt->execute();
    }

    /**
     * Met à jour le statut d'une invitation.
     * 
     * @param int $idHote L'identifiant de l'utilisateur hôte.
     * @param int $idInvite L'identifiant de l'utilisateur invité.
     * @param int $idGroupe L'identifiant du groupe.
     * @param string $statut Le nouveau statut ('accepte' ou 'refuse').
     * @return bool true si la mise à jour a réussi, false sinon.
     */
    public function mettreAJourStatut(int $idHote, int $idInvite, int $idGroupe, string $statut): bool
    {
        $stmt = $this->conn->prepare("
            UPDATE inviter 
            SET statut = :statut 
            WHERE idHote = :idHote AND idInvite = :idInvite AND idGroupe = :idGroupe
        ");
        $stmt->bindValue(':statut', $statut, PDO::PARAM_STR);
        $stmt->bindValue(':idHote', $idHote, PDO::PARAM_INT);
        $stmt->bindValue(':idInvite', $idInvite, PDO::PARAM_INT);
        $stmt->bindValue(':idGroupe', $idGroupe, PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * Vérifie s'il existe déjà une invitation en attente.
     * 
     * @param int $idGroupe L'identifiant du groupe.
     * @param int $idHote L'identifiant de l'utilisateur hôte.
     * @param int $idInvite L'identifiant de l'utilisateur invité.
     * @return bool true s'il existe une invitation en attente, false sinon.
     */
    public function existeInvitationEnAttente(int $idGroupe, int $idHote, int $idInvite): bool
    {
        $stmt = $this->conn->prepare("
            SELECT COUNT(*) as count 
            FROM inviter 
            WHERE idGroupe = :idGroupe 
            AND idHote = :idHote
            AND idInvite = :idInvite 
            AND statut = 'en_attente'
        ");
        $stmt->bindValue(':idGroupe', $idGroupe, PDO::PARAM_INT);
        $stmt->bindValue(':idHote', $idHote, PDO::PARAM_INT);
        $stmt->bindValue(':idInvite', $idInvite, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result['count'] > 0;
    }

    /**
     * Supprime une invitation.
     * 
     * @param int $idHote L'identifiant de l'utilisateur hôte.
     * @param int $idInvite L'identifiant de l'utilisateur invité.
     * @param int $idGroupe L'identifiant du groupe.
     * @param string $dateInvitation La date de l'invitation (partie de la clé primaire).
     * @return bool true si la suppression a réussi, false sinon.
     */
    public function supprimerInvitation(int $idHote, int $idInvite, int $idGroupe, string $dateInvitation): bool
    {
        $stmt = $this->conn->prepare("DELETE FROM inviter WHERE idHote = :idHote AND idInvite = :idInvite AND idGroupe = :idGroupe AND dateInvitation = :dateInvitation");
        $stmt->bindValue(':idHote', $idHote, PDO::PARAM_INT);
        $stmt->bindValue(':idInvite', $idInvite, PDO::PARAM_INT);
        $stmt->bindValue(':idGroupe', $idGroupe, PDO::PARAM_INT);
        $stmt->bindValue(':dateInvitation', $dateInvitation, PDO::PARAM_STR);

        return $stmt->execute();
    }
}
?>
