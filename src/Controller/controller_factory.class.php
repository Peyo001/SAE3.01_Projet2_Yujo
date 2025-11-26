<?php
/**
 * Classe ControllerFactory
 * 
 * Cette classe est une fabrique de controlleurs. Elle permet de créer des instances de coontrolleurs en fonction de leurs noms.
 * 
 * Exemple d'utilsation :
 * $controller = ControllerFactory::getController('Home', $loader, $twig);
 * 
 */

class ControllerFactory
{
    public static function getController(string $nom, \Twig\Loader\FilesystemLoader $loader, \Twig\Environment $twig) : object
    {
        $controllerName = 'Controller' . $nom;
        if (!class_exists($controllerName)) {
            throw new Exception("Le controleur $controllerName n'existe pas");
        }
        return new $controllerName($loader, $twig);
    }
}