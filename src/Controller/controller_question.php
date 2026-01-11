<?php

class ControllerQuestion extends Controller
{
	public function __construct(\Twig\Loader\FilesystemLoader $loader, \Twig\Environment $twig)
	{
		parent::__construct($loader, $twig);
	}

	/**
	 * LISTE DES QUESTIONS
	 */
	public function lister(): void
	{
		$dao = new QuestionDAO($this->getPdo());
		$questions = $dao->findAll();

		echo $this->getTwig()->render('liste_questions.twig', [
			'questions' => $questions,
			'title' => 'Liste des questions'
		]);
	}

	/**
	 * AFFICHER UNE QUESTION
	 */
	public function afficher(): void
	{
		if (!isset($_GET['idQuestion'])) {
			header('Location: index.php?controleur=question&methode=lister');
			exit;
		}

		$dao = new QuestionDAO($this->getPdo());
		$question = $dao->findByIdQuestion((int) $_GET['idQuestion']);

		if (!$question) {
			echo "Question introuvable.";
			return;
		}

		echo $this->getTwig()->render('question.twig', [
			'question' => $question
		]);
	}

	/**
	 * FORMULAIRE AJOUT QUESTION
	 */
	public function afficherFormulaireInsertion(): void
	{
		echo $this->getTwig()->render('ajout_question.twig', [
			'title' => 'Nouvelle question'
		]);
	}

	/**
	 * TRAITEMENT FORMULAIRE AJOUT QUESTION
	 */
	public function traiterFormulaireInsertion(): void
	{
		if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
			header('Location: index.php?controleur=question&methode=afficherFormulaireInsertion');
			exit;
		}

		$libelle = $this->sanitize($_POST['libelle'] ?? '');

		if ($libelle === '') {
			echo "Le libellé est requis.";
			return;
		}

		$question = new Question(null, $libelle);
		$dao = new QuestionDAO($this->getPdo());
		$ok = $dao->createQuestion($question);

		if ($ok && $question->getIdQuestion() !== null) {
			header('Location: index.php?controleur=question&methode=afficher&idQuestion=' . $question->getIdQuestion());
			exit;
		}

		echo "Erreur lors de la création de la question.";
	}

	/**
	 * MISE À JOUR D'UNE QUESTION
	 */
	public function mettreAJour(): void
	{
		if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
			echo "Méthode non autorisée.";
			return;
		}

		$id = isset($_POST['idQuestion']) ? (int) $_POST['idQuestion'] : 0;
		$libelle = $this->sanitize($_POST['libelle'] ?? '');

		if ($id <= 0 || $libelle === '') {
			echo "Identifiant et libellé requis.";
			return;
		}

		$dao = new QuestionDAO($this->getPdo());
		$question = new Question($id, $libelle);
		$ok = $dao->update($question);

		if ($ok) {
			header('Location: index.php?controleur=question&methode=afficher&idQuestion=' . $id);
			exit;
		}

		echo "Erreur lors de la mise à jour de la question.";
	}

	/**
	 * SUPPRESSION D'UNE QUESTION (INTERDITE)
	 * Une question ne peut pas être supprimée seule.
	 * Il faut supprimer le quiz associé pour la supprimer.
	 */
	public function supprimer(): void
	{
		echo "Suppression de question interdite. Supprimez le quiz associé.";
	}

	/**
	 * AJOUTER UNE RÉPONSE POSSIBLE À LA QUESTION
	 */
	public function ajouterReponse(): void
	{
		if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
			echo "Méthode non autorisée.";
			return;
		}

		$idQuestion = isset($_POST['idQuestion']) ? (int) $_POST['idQuestion'] : 0;
		$libelle = $this->sanitize($_POST['libelle'] ?? '');
		$estCorrecte = isset($_POST['estCorrecte']) ? (bool) $_POST['estCorrecte'] : false;

		if ($idQuestion <= 0 || $libelle === '') {
			echo "Question et libellé requis.";
			return;
		}

		$dao = new QuestionDAO($this->getPdo());
		$reponse = new ReponsePossible(null, $libelle, $estCorrecte);
		$ok = $dao->addReponseToQuestion($idQuestion, $reponse);

		if ($ok) {
			header('Location: index.php?controleur=question&methode=afficher&idQuestion=' . $idQuestion);
			exit;
		}

		echo "Erreur lors de l'ajout de la réponse.";
	}
}

