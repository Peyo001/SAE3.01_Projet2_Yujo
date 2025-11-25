<?php

class Config {
    /**
     * Classe Config
     *
     * Cette classe gère les paramètres de configuration stockés dans un fichier JSON.
     * Elle permet de récupérer des paramètres spécifiques via une clé donnée.
     *
     */

    private static $configPath = __DIR__ . 'config.json';
    private static $dictParameters = null;

    /**
     * Cette méthode récupère les paramètres de configuration depuis un fichier JSON.
     *
     * @return array Tableau associatif des paramètres de configuration.
     * @throws Exception Si le fichier de configuration est introuvable.
     */
    private static function getConfiguration() {
        
        if (!file_exists(Config::$configPath)) {
            throw new Exception("La configuration est introuvable: " . Config::$configPath);
        }
        $jsonContent = file_get_contents(Config::$configPath);
        
        return json_decode($jsonContent, true);
    }


    /**
     * Cette méthode récupère un paramètre de configuration spécifique en fonction de la clé fournie.
     *
     * @param string $key La clé du paramètre de configuration à récupérer.
     * @return mixed La valeur du paramètre de configuration.
     * @throws Exception Si le fichier de configuration est introuvable ou si la clé n'existe pas.
     */
    public static function getParametre($key) {
        if (Config::$dictParameters === null) {
            Config::$dictParameters = Config::getConfiguration();
        }

        if (!array_key_exists($key, Config::$dictParameters)) {
            throw new Exception("Parameter not found in configuration: " . $key);
        }

        return Config::$dictParameters[$key];
    }
}