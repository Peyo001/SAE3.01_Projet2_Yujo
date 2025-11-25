<?php

class ControllerFactory
{
    public static function getController(string $nom, \Twig\Loader\FilesystemLoader $loader, \Twig\Environment $twig) : object
    {
        $controllerName = 'Controller' . $nom;
        if (!class_exists($controllerName)) {
<<<<<<< HEAD
            throw new Exception("Le controleur $controllerName n'existe pas");
=======
            throw new Exception("La controleur $controllerName n'existe pas");
>>>>>>> c44f315ee8a16e01a37b5011c3a47ce7c02970a9
        }
        return new $controllerName($loader, $twig);
    }
}