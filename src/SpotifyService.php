<?php

namespace App;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Exception;

class SpotifyService {
    private $client;
    private $tokenCachePath;
    private $storagePath;
    
    public function __construct() {
        $this->client = new Client([
            'base_uri' => 'https://api.spotify.com/v1/',
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ]
        ]);
        
        $this->storagePath = __DIR__ . '/../storage';
        $this->tokenCachePath = $this->storagePath . '/token.cache.json';
        
        $this->ensureStorageDirectory();
    }
    
    private function ensureStorageDirectory() {
        if (!file_exists($this->storagePath)) {
            if (!mkdir($this->storagePath, 0755, true)) {
                throw new Exception('Failed to create storage directory');
            }
        }
        
        if (!is_writable($this->storagePath)) {
            throw new Exception('Storage directory is not writable');
        }
    }
    
    public function getAccessToken() {
        if ($this->hasValidCachedToken()) {
            return $this->getCachedToken()['access_token'];
        }
        
        $token = $this->requestNewToken();
        $this->cacheToken($token);
        
        return $token['access_token'];
    }
    
    private function hasValidCachedToken() {
        if (!file_exists($this->tokenCachePath)) {
            return false;
        }
        
        $cached = $this->getCachedToken();
        return $cached && 
               isset($cached['expires_at']) && 
               $cached['expires_at'] > time();
    }
    
    private function getCachedToken() {
        try {
            if (!file_exists($this->tokenCachePath)) {
                return null;
            }
            
            $content = file_get_contents($this->tokenCachePath);
            if ($content === false) {
                throw new Exception('Failed to read token cache file');
            }
            
            $token = json_decode($content, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Invalid token cache format');
            }
            
            return $token;
        } catch (Exception $e) {
            // If there's any error reading the cache, we'll return null and get a new token
            error_log('Token cache read error: ' . $e->getMessage());
            return null;
        }
    }
    
    private function requestNewToken() {
        try {
            $client = new Client(['base_uri' => 'https://accounts.spotify.com/api/']);
            
            $clientId = Config::get('SPOTIFY_CLIENT_ID');
            $clientSecret = Config::get('SPOTIFY_CLIENT_SECRET');
            
            if (!$clientId || !$clientSecret) {
                throw new Exception('Spotify API credentials not configured');
            }
            
            $response = $client->post('token', [
                'headers' => [
                    'Authorization' => 'Basic ' . base64_encode($clientId . ':' . $clientSecret)
                ],
                'form_params' => [
                    'grant_type' => 'client_credentials'
                ]
            ]);
            
            $token = json_decode($response->getBody()->getContents(), true);
            if (!isset($token['access_token']) || !isset($token['expires_in'])) {
                throw new Exception('Invalid token response from Spotify');
            }
            
            $token['expires_at'] = time() + $token['expires_in'];
            return $token;
            
        } catch (GuzzleException $e) {
            throw new Exception('Failed to obtain Spotify access token: ' . $e->getMessage());
        }
    }
    
    private function cacheToken($token) {
        try {
            $jsonToken = json_encode($token);
            if ($jsonToken === false) {
                throw new Exception('Failed to encode token data');
            }
            
            if (file_put_contents($this->tokenCachePath, $jsonToken) === false) {
                throw new Exception('Failed to write token cache file');
            }
        } catch (Exception $e) {
            error_log('Token cache write error: ' . $e->getMessage());
            // We'll continue even if caching fails
        }
    }
    
    public function searchPlaylists($query, $limit = 20) {
        try {
            $response = $this->client->get('search', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->getAccessToken()
                ],
                'query' => [
                    'q' => $query,
                    'type' => 'playlist',
                    'limit' => $limit
                ]
            ]);
            
            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            throw new \Exception('Failed to search playlists: ' . $e->getMessage());
        }
    }
}
