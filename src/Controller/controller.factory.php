<?php

/**
 * @file controller_factory.class.php
 * @brief Ce fichier contient la classe ControllerFactory pour créer des instances de contrôleurs.
 */

/**
 * @brief Classe ControllerFactory pour créer des instances de contrôleurs.
 *
 * @details Cette classe utilise un design pattern Factory simplifié pour instancier les contrôleurs métiers.
 */
class ControllerFactory
{
    /**
     * @brief Crée une instance d'un contrôleur.
     *
     * @param string $controleur Nom du contrôleur à instancier.
     * @param \Twig\Loader\FilesystemLoader $loader Chargeur de templates Twig.
     * @param \Twig\Environment $twig Environnement Twig.
     * @return object Instance d'un contrôleur métier.
     * @throws Exception Si le contrôleur n'existe pas.
     * @warning Le design pattern Factory est un plus complexe que ce que nous utilisons ici.
     * @remark Cette méthode est statique.
     */
    public static function getController(string $controleur, \Twig\Loader\FilesystemLoader $loader, \Twig\Environment $twig): object
    {
        $controllerName = 'Controller' . $controleur;
        //test si la controleur existe
        if (!class_exists($controllerName)) {
            throw new Exception("La controleur $controllerName n'existe pas");
        }
        return new $controllerName($loader, $twig);
    }
}
