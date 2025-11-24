<?php

class ControllerFactory
{
    public static function getController(string $nom, \Twig\Loader\FilesystemLoader $loader, \Twig\Environment $twig) : object
    {
        $controllerName = 'Controller' . $nom;
        if (!class_exists($controllerName)) {
            throw new Exception("La controleur $controllerName n'existe pas");
        }
        return new $controllerName($loader, $twig);
    }
}