<?php

/**
 * ContrôleurBoutique gère les actions liées à la boutique en ligne comme
 * son affichage, l'achat d'objets et l'achat de YuPoints.
 *
 * Hérite de la classe Controller pour bénéficier des fonctionnalités de base.
 * Utilise Twig pour le rendu des vues.
 * Utilise la classe Validator pour la validation des données.
 * 
 * Exemples d'utilisation :
 * $controller = new ControllerBoutique($loader, $twig);
 * $controller->afficher(); // Affiche la boutique
 * $controller->acheter(); // Achète un objet
 * $controller->acheterPoints(); // Achète des YuPoints
 */
class ControllerBoutique extends Controller
{
	/**
	 * @brief Constructeur de la classe ControllerBoutique.
	 * 
	 * @param \Twig\Loader\FilesystemLoader $loader Le chargeur de fichiers Twig.
	 * @param \Twig\Environment $twig L'environnement Twig pour le rendu des vues.
	 */
	public function __construct(\Twig\Loader\FilesystemLoader $loader, \Twig\Environment $twig)
	{
		parent::__construct($loader, $twig);
	}

	/**
	 * @brief Affiche la page de la boutique avec les objets disponibles et le solde YuPoints de l'utilisateur.
	 * Vérifie que l'utilisateur est connecté.
	 * 
	 * @return void
	 */
	public function afficher(): void
	{
		$objetsManager = new ObjetDao($this->getPdo());
		$objets = $objetsManager->findAll();
		$yuPoints = null;
		$idUtilisateur = $this->retourPageConnexion();

		$objetPossedeParID = [];
		
		$utilisateurDao = new UtilisateurDao($this->getPdo());
		$utilisateur = $utilisateurDao->find((int)$idUtilisateur);
		$yuPoints = $utilisateur ? $utilisateur->getYuPoints() : null;
		$achatDao = new AchatDao($this->getPdo());
		$objetPossedeParID = $achatDao->listObjetsAchetesByUtilisateur((int)$idUtilisateur);
		
		
		echo $this->getTwig()->render('boutique.twig', [
			'objets' => $objets,
			'yuPoints' => $yuPoints,
			'utilisateurConnecte' => $idUtilisateur,
			'objetPossedeParID' => $objetPossedeParID
		]);
	}

	/**
	 * @brief Traite l'achat d'un objet: vérifie YuPoints et enregistre l'achat.
	 * Vérifie que l'utilisateur est connecté.
	 * Attendu: POST avec `idObjet`.
	 * 
	 * @return void
	 */
	public function acheter(): void
	{
		if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
			header('Location: index.php?controleur=boutique&methode=afficher');
			exit;
		}

		$idUtilisateur = $this->retourPageConnexion();

		$idObjet = isset($_POST['idObjet']) ? (int)$_POST['idObjet'] : 0;
		if ($idObjet <= 0) {
			echo "Objet invalide.";
			return;
		}

		$objetDao = new ObjetDao($this->getPdo());
		$utilisateurDao = new UtilisateurDao($this->getPdo());
		$achatDao = new AchatDao($this->getPdo());

		$objet = $objetDao->find($idObjet);
		$utilisateur = $utilisateurDao->find((int)$idUtilisateur);

		if (!$objet || !$utilisateur) {
			echo "Utilisateur ou objet introuvable.";
			return;
		}

		$prix = $objet->getPrix();
		$solde = $utilisateur->getYuPoints();

		// Déjà acheté ?
		$dejaAchete = $achatDao->findByObjetUtilisateur($idObjet, (int)$idUtilisateur);
		if ($dejaAchete) {
			echo $this->getTwig()->render('boutique.twig', [
				'objets' => $objetDao->findAll(),
				'yuPoints' => $solde,
				'utilisateurConnecte' => $idUtilisateur,
				'message' => 'Vous possédez déjà cet objet.'
			]);
			return;
		}

		if ($solde < $prix) {
			echo $this->getTwig()->render('boutique.twig', [
				'objets' => $objetDao->findAll(),
				'yuPoints' => $solde,
				'utilisateurConnecte' => $idUtilisateur,
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
					'utilisateurConnecte' => $idUtilisateur,
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
			'utilisateurConnecte' => $idUtilisateur,
			'objetPossedeParID' => $achatDao->listObjetsAchetesByUtilisateur((int)$idUtilisateur),
			'message' => 'Achat effectué avec succès!'
		]);
	}

	/**
	 * @brief Ajoute des YuPoints au solde utilisateur (pack prédéfinis).
	 * Attendu: POST pack in [100, 500, 1000].
	 * 
	 * @return void
	 */
	public function acheterPoints(): void
	{
		if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
			header('Location: index.php?controleur=boutique&methode=afficher');
			exit;
		}

		$idUtilisateur = $this->retourPageConnexion();
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

		$utilisateur = $utilisateurDao->find((int)$idUtilisateur);
		$solde = $utilisateur ? $utilisateur->getYuPoints() : null;

		echo $this->getTwig()->render('boutique.twig', [
			'objets' => $objetDao->findAll(),
			'yuPoints' => $solde,
			'utilisateurConnecte' => $idUtilisateur,
			'objetPossedeParID' => $achatDao->listObjetsAchetesByUtilisateur((int)$idUtilisateur),
			'message' => "+" . $pack . " YuPoints ajoutés à votre solde."
		]);
	}
}

