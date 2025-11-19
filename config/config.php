<?php

class Config {
    /*
    Class to manage configuration parameters stored in a JSON file.
    */

    private static $configPath = __DIR__ . 'config.json';
    private static $dictParameters = null;

    private static function getJson() {
        
        if (!file_exists($configPath)) {
            throw new Exception("Configuration file not found: " . $configPath);
        }
        $jsonContent = file_get_contents($configPath);
        
        return json_decode($jsonContent, true);
    }

    public static function getParameter($key) {
        if (Config::$dictParameters === null) {
            Config::$dictParameters = Config::getJson();
        }

        if (!array_key_exists($key, Config::$dictParameters)) {
            throw new Exception("Parameter not found in configuration: " . $key);
        }

        return Config::$dictParameters[$key];
    }
}