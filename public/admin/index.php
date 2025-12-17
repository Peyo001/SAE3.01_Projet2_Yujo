<?php
    session_start();

    require_once __DIR__ . '/../../include.php';

    // Par défaut : admin dashboard
    $controllerName = $_GET['controller'] ?? 'Admin';
    $methode = $_GET['methode'] ?? 'dashboard';

    $controleur = ControllerFactory::getController($controllerName, $loader, $twig);
    $controleur->call($methode);
