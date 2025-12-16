<?php

/**
 * Classe Validator pour la validation des données de formulaire.
 * 
 * Cette classe permet de définir des règles de validation pour différents champs
 * et de valider les données fournies en fonction de ces règles. Elle gère les
 * messages d'erreur standardisés pour chaque type de validation échouée.
 * 
 * Exemple d'utilisation :
 * $reglesValidation = [
 *          'nom' => [
 *              'obligatoire' => false,
 *              'type' => 'string',
 *               'longueur_min' => 1,
 *               'longueur_max' => 100,
 *                // Lettres, accents, apostrophes et traits d'union
 *               'format' => '/^[a-zA-ZÀ-ÿ\'-]+$/'
 *               
 *           ],
 *           'prenom' => [
 *               'obligatoire' => true,
 *               'type' => 'string',
 *               'longueur_min' => 1,
 *               'longueur_max' => 100,
 * *               // Lettres et caractères accentués uniquement
 *               'format' => '/^[a-zA-ZÀ-ÿ\'-]+$/' 
 *           ],
 * 
 * ];   
 * 
 * $validator = new Validator($regles);
 * $donnees = $validator->valider($_POST);
 * $erreurs = $validator->getMessagesErreurs();
 * if ($donnees) {  
 *   // Données valides, procéder au traitement
 * } else {
 *   $erreurs = $validator->getMessagesErreurs();
 * }
 */


  
class Validator
{
    private array $regles; // Les règles de validation à vérifier
    private array $messagesErreurs = []; // Les messages d'erreurs standardisés.

    /**
     * @brief Constructeur de la classe Validator qui prend en paramètre un tableau de règles de validation.
     *
     * @param array $regles Un tableau associatif définissant les règles de validation pour chaque champ.
     */
    public function __construct(array $regles)
    {
        $this->regles = $regles;
    }

    /**
     * @brief Valide les données fournies par rapport aux règles de validation définies.
     *
     * @param array $donnees Un tableau associatif des données du formulaire.
     * @return bool Retourne true si toutes les validations sont réussies, false sinon.
     */
    public function valider(array $donnees): bool
    {
        $valide = true;
        $this->messagesErreurs = []; // Réinitialisation des erreurs à chaque validation

        foreach ($this->regles as $champ => $reglesChamp)
        {
            $valeur = $donnees[$champ] ?? null;

            if (!$this->validerChamp($champ, $valeur, $reglesChamp))
            {
                $valide = false;
            }
        }

        return $valide;
    }

    /**
     * @brief Valide un champ spécifique en fonction de ses règles.
     *
     * @param string $champ Le nom du champ.
     * @param mixed $valeur La valeur du champ.
     * @param array $regles Un tableau de règles pour le champ.
     * @return bool Retourne true si toutes les validations du champ sont réussies, false sinon.
     */
    private function validerChamp(string $champ, mixed $valeur, array $regles): bool
    {
        $estValide = true;

        // 1. Vérification de la règle "obligatoire" avant toute autre validation.
        if (isset($regles['obligatoire']) && $regles['obligatoire'] && empty($valeur))
        {
            $this->messagesErreurs[] = "Le champ $champ est obligatoire.";
            return false; // Arrêter ici si le champ est obligatoire et vide
        }

        // 2. Si le champ est vide et non obligatoire, aucune autre validation n'est nécessaire
        if (empty($valeur) && (!isset($regles['obligatoire']) || !$regles['obligatoire']))
        {
            return true;
        }

        // Validation des autres règles pour les champs non vides ou obligatoires remplis.
        foreach ($regles as $regle => $parametre)
        {
            switch ($regle)
            {
                case 'type':
                    if ($parametre === 'string' && !is_string($valeur))
                    {
                        $this->messagesErreurs[] = "Le champ $champ doit être une chaîne de caractères.";
                        $estValide = false;
                    }
                    elseif ($parametre === 'integer' && !filter_var($valeur, FILTER_VALIDATE_INT))
                    {
                        $this->messagesErreurs[] = "Le champ $champ doit être un nombre entier.";
                        $estValide = false;
                    }
                    elseif ($parametre === 'numeric' && !is_numeric($valeur))
                    {
                        $this->messagesErreurs[] = "Le champ $champ doit être une valeur numérique.";
                        $estValide = false;
                    }
                    break;
                case 'longueur_min':
                    if (strlen($valeur) < $parametre)
                    {
                        $this->messagesErreurs[] = "Le champ $champ doit comporter au moins $parametre caractères.";
                        $estValide = false;
                    }
                    break;
                case 'longueur_max':
                    if (strlen($valeur) > $parametre)
                    {
                        $this->messagesErreurs[] = "Le champ $champ ne doit pas dépasser $parametre caractères.";
                        $estValide = false;
                    }
                    break;
                case 'longueur_exacte':
                    if (strlen($valeur) !== $parametre)
                    {
                        $this->messagesErreurs[] = "Le champ $champ doit comporter exactement $parametre caractères.";
                        $estValide = false;
                    }
                    break;
                case 'format':
                    // Gestion des dates via DateTime::createFromFormat
                    if (($regles['type'] ?? '') === 'date' && is_string($parametre)) {
                        $dt = \DateTime::createFromFormat($parametre, $valeur);
                        $errors = \DateTime::getLastErrors();
                        if (!$dt || ($errors['warning_count'] ?? 0) > 0 || ($errors['error_count'] ?? 0) > 0) {
                            $this->messagesErreurs[] = "Le format du champ $champ est invalide.";
                            $estValide = false;
                        }
                    }
                    // Regex : on exige un délimiteur (ex : /.../)
                    elseif (is_string($parametre) && substr($parametre, 0, 1) === '/' && !preg_match($parametre, $valeur))
                    {
                        $this->messagesErreurs[] = "Le format du champ $champ est invalide. Le mots de passe doit contenir au moins une majuscule, une minuscule, un chiffre et un caractère spécial.";
                        $estValide = false;
                    }
                    elseif ($parametre === FILTER_VALIDATE_EMAIL && !filter_var($valeur, FILTER_VALIDATE_EMAIL))
                    {
                        $this->messagesErreurs[] = "L'adresse email est invalide.";
                        $estValide = false;
                    }
                    elseif ($parametre === FILTER_VALIDATE_URL && !filter_var($valeur, FILTER_VALIDATE_URL))
                    {
                        $this->messagesErreurs[] = "L'URL du site web est invalide.";
                        $estValide = false;
                    }
                    break;
                case 'plage_min':
                    if ($valeur < $parametre)
                    {
                        $this->messagesErreurs[] = "La valeur de $champ doit être au minimum $parametre.";
                        $estValide = false;
                    }
                    break;
                case 'plage_max':
                    if ($valeur > $parametre)
                    {
                        $this->messagesErreurs[] = "La valeur de $champ ne doit pas dépasser $parametre.";
                        $estValide = false;
                    }
                    break;
            }
        }

        return $estValide;
    }

    /**
     * @brief Retourne les messages d'erreur générés lors de la validation.
     *
     * @return array Un tableau contenant les messages d'erreur pour chaque champ non valide.
     */
    public function getMessagesErreurs(): array
    {
        return $this->messagesErreurs;
    }
}
