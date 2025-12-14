<?php
/**
 * @file index.php
 * @brief Point d'entrée principal de l'application.
 *
 * @details Ce fichier gère la logique principale de routage de l'application, 
 * en chargeant le contrôleur et la méthode appropriés en fonction des paramètres GET.
 */

require_once __DIR__ . '../../include.php';

try
{
    // Détermine le contrôleur à utiliser et la méthode à appeler
    if (isset($_GET['controleur']))
    {
        $controllerName = $_GET['controleur'];
    }
    else
    {
        $controllerName = '';
    }
    if (isset($_GET['methode']))
    {
        $methode = $_GET['methode'];
    }
    else
    {
        $methode = '';
    }

    //Gestion de la page index.php sans paramètres
    if ($controllerName == '' && $methode == '')
    {
        $controllerName = "Utilisateur"; //contrôleur utilisateur 
        $methode = "connexion"; //methode affichant le formulaire de connexion
    }
    else
    {
        if ($controllerName == '')
        {
            throw new Exception("Il manque le contrôleur");
        }
        if ($methode == '')
        {
            throw new Exception("Il manque la méthode");
        }
    }

    $controleur = ControllerFactory::getController($controllerName, $loader, $twig);
    $controleur->call($methode);
}
catch (Exception $e)
{
    die('Erreur : ' . $e->getMessage());
}