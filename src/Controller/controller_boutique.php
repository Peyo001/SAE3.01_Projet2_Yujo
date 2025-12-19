<?php

/**
 * Contrôleur Boutique
 *
 * Liste tous les objets disponibles et permet l'achat
 * en vérifiant le nombre de YuPoints de l'utilisateur.
 */
class ControllerBoutique extends Controller
{
	public function __construct(\Twig\Loader\FilesystemLoader $loader, \Twig\Environment $twig)
	{
		parent::__construct($loader, $twig);
	}

	/**
	 * Affiche la boutique avec tous les objets et le solde utilisateur.
	 */
	public function afficher(): void
	{
		$objetDao = new ObjetDao($this->getPdo());
		$objets = $objetDao->findAll();

		$yuPoints = null;
		$idUtilisateur = $_SESSION['idUtilisateur'] ?? null;
		$ownedObjectIds = [];
		if ($idUtilisateur) {
			$utilisateurDao = new UtilisateurDao($this->getPdo());
			$user = $utilisateurDao->find((int)$idUtilisateur);
			$yuPoints = $user ? $user->getYuPoints() : null;
			$achatDao = new AchatDao($this->getPdo());
			$ownedObjectIds = $achatDao->listObjetsAchetesByUtilisateur((int)$idUtilisateur);
		}

		echo $this->getTwig()->render('boutique.twig', [
			'objets' => $objets,
			'yuPoints' => $yuPoints,
			'user_connected' => $idUtilisateur,
			'ownedObjectIds' => $ownedObjectIds
		]);
	}

	/**
	 * Traite l'achat d'un objet: vérifie YuPoints et enregistre l'achat.
	 * Attendu: POST avec `idObjet`.
	 */
	public function acheter(): void
	{
		if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
			header('Location: index.php?controleur=boutique&methode=afficher');
			exit;
		}

		$idUtilisateur = $_SESSION['idUtilisateur'] ?? null;
		if (!$idUtilisateur) {
			echo "Vous devez être connecté pour acheter.";
			return;
		}

		$idObjet = isset($_POST['idObjet']) ? (int)$_POST['idObjet'] : 0;
		if ($idObjet <= 0) {
			echo "Objet invalide.";
			return;
		}

		$objetDao = new ObjetDao($this->getPdo());
		$utilisateurDao = new UtilisateurDao($this->getPdo());
		$achatDao = new AchatDao($this->getPdo());

		$objet = $objetDao->find($idObjet);
		$user = $utilisateurDao->find((int)$idUtilisateur);

		if (!$objet || !$user) {
			echo "Utilisateur ou objet introuvable.";
			return;
		}

		$prix = $objet->getPrix();
		$solde = $user->getYuPoints();

		// Déjà acheté ?
		$dejaAchete = $achatDao->findByObjetUtilisateur($idObjet, (int)$idUtilisateur);
		if ($dejaAchete) {
			echo $this->getTwig()->render('boutique.twig', [
				'objets' => $objetDao->findAll(),
				'yuPoints' => $solde,
				'user_connected' => $idUtilisateur,
				'message' => 'Vous possédez déjà cet objet.'
			]);
			return;
		}

		if ($solde < $prix) {
			echo $this->getTwig()->render('boutique.twig', [
				'objets' => $objetDao->findAll(),
				'yuPoints' => $solde,
				'user_connected' => $idUtilisateur,
				'message' => 'Solde insuffisant pour cet achat.'
			]);
			return;
		}

		// Début transaction pour cohérence solde + achat
		$pdo = $this->getPdo();
		try {
			$pdo->beginTransaction();

			// Déduire les YuPoints (via DAO)
			$ok = $utilisateurDao->decrementerYuPoints((int)$idUtilisateur, (int)$prix);
			if (!$ok) {
				$pdo->rollBack();
				echo $this->getTwig()->render('boutique.twig', [
					'objets' => $objetDao->findAll(),
					'yuPoints' => $solde,
					'user_connected' => $idUtilisateur,
					'message' => 'Solde insuffisant pour cet achat.'
				]);
				return;
			}

			// Enregistrer l'achat
			$achat = new Achat($idObjet, date('Y-m-d H:i:s'), (int)$idUtilisateur);
			$achatDao->insererAchat($achat);

			$pdo->commit();
		} catch (Exception $e) {
			$pdo->rollBack();
			echo "Erreur lors de l'achat: " . htmlspecialchars($e->getMessage());
			return;
		}

		// Retour à la boutique avec message de succès
		$nouveauSolde = $solde - $prix;
		echo $this->getTwig()->render('boutique.twig', [
			'objets' => $objetDao->findAll(),
			'yuPoints' => $nouveauSolde,
			'user_connected' => $idUtilisateur,
			'ownedObjectIds' => $achatDao->listObjetsAchetesByUtilisateur((int)$idUtilisateur),
			'message' => 'Achat effectué avec succès!'
		]);
	}

	/**
	 * Ajoute des YuPoints au solde utilisateur (pack prédéfinis).
	 * Attendu: POST pack in [100, 500, 1000].
	 */
	public function acheterPoints(): void
	{
		if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
			header('Location: index.php?controleur=boutique&methode=afficher');
			exit;
		}

		$idUtilisateur = $_SESSION['idUtilisateur'] ?? null;
		if (!$idUtilisateur) {
			echo "Vous devez être connecté pour acheter des YuPoints.";
			return;
		}

		$pack = isset($_POST['pack']) ? (int)$_POST['pack'] : 0;
		$packsAutorises = [100, 500, 1000];
		if (!in_array($pack, $packsAutorises, true)) {
			echo "Pack invalide.";
			return;
		}

		$utilisateurDao = new UtilisateurDao($this->getPdo());
		$objetDao = new ObjetDao($this->getPdo());
		$achatDao = new AchatDao($this->getPdo());

		$ok = $utilisateurDao->incrementerYuPoints((int)$idUtilisateur, $pack);
		if (!$ok) {
			echo "Impossible de créditer les YuPoints.";
			return;
		}

		$user = $utilisateurDao->find((int)$idUtilisateur);
		$solde = $user ? $user->getYuPoints() : null;

		echo $this->getTwig()->render('boutique.twig', [
			'objets' => $objetDao->findAll(),
			'yuPoints' => $solde,
			'utilisateurConnecte' => $idUtilisateur,
			'objetPossedeParID' => $achatDao->listObjetsAchetesByUtilisateur((int)$idUtilisateur),
			'message' => "+" . $pack . " YuPoints ajoutés à votre solde."
		]);
	}
}

