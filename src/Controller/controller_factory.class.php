<?php
/**
 * Classe ControllerFactory
 * 
 * Cette classe est une fabrique de controlleurs. Elle permet de créer des instances de controlleurs en fonction de leurs noms.
 * instanciation dynamique des controlleurs avec Twig pour le rendu des templates.
 * 
 * 
 * Exemple d'utilsation :
 * $controller = ControllerFactory::getController('Home', $loader, $twig);
 * 
 */

class ControllerFactory
{
    /**
     * Méthode statique pour obtenir une instance de contrôleur.
     * 
     * Cette méthode permet de créer dynamiquement une instance d'un contrôleur en fonction du nom du contrôleur passé en paramètre.
     * Le contrôleur est instancié avec un `FilesystemLoader` et un `Twig\Environment`, qui peuvent être utilisés pour charger les templates Twig.
     * 
     * @param string $nom Le nom du contrôleur à créer (sans le préfixe "Controller").
     * @param \Twig\Loader\FilesystemLoader $loader L'objet loader pour charger les fichiers Twig.
     * @param \Twig\Environment $twig L'objet environnement Twig pour rendre les templates.
     * @return object Une instance du contrôleur demandé.
     * @throws Exception Si la classe du contrôleur n'existe pas, une exception est levée.
     */
    public static function getController(string $nom, \Twig\Loader\FilesystemLoader $loader, \Twig\Environment $twig) : object
    {
        $controllerName = 'Controller' . $nom;
        if (!class_exists($controllerName)) {
            throw new Exception("Le controleur $controllerName n'existe pas");
        }
        return new $controllerName($loader, $twig);
    }
}