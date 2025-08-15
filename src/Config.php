<?php

namespace App;

use Dotenv\Dotenv;
use Exception;

class Config {
    private static $instance = null;
    private $env = [];

    private function __construct() {
        try {
            $envPath = __DIR__ . '/..';
            if (!file_exists($envPath . '/.env')) {
                throw new Exception('Environment file not found. Please copy .env.example to .env');
            }
            
            $dotenv = Dotenv::createImmutable($envPath);
            $dotenv->load();
            $dotenv->required(['SPOTIFY_CLIENT_ID', 'SPOTIFY_CLIENT_SECRET'])->notEmpty();
            
            $this->env = $_ENV;
        } catch (Exception $e) {
            throw new Exception('Configuration Error: ' . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public static function get($key, $default = null) {
        return self::getInstance()->env[$key] ?? $default;
    }
}
