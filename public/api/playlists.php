<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use App\Config;
use App\SpotifyService;
use App\PlaylistService;

// Set error handling
set_error_handler(function($severity, $message, $file, $line) {
    throw new ErrorException($message, 0, $severity, $file, $line);
});

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Basic rate limiting
session_start();
$currentMinute = floor(time() / 60);
$requestCount = $_SESSION['request_count'] ?? [];
$ipAddress = $_SERVER['REMOTE_ADDR'];

if (!isset($requestCount[$currentMinute])) {
    $requestCount = [$currentMinute => []];
}

if (!isset($requestCount[$currentMinute][$ipAddress])) {
    $requestCount[$currentMinute][$ipAddress] = 0;
}

if ($requestCount[$currentMinute][$ipAddress] >= 60) {
    http_response_code(429);
    echo json_encode(['error' => true, 'message' => 'Rate limit exceeded. Please try again in a minute.']);
    exit;
}

$requestCount[$currentMinute][$ipAddress]++;
$_SESSION['request_count'] = $requestCount;

// Process request
try {
    $spotifyService = new SpotifyService();
    $playlistService = new PlaylistService($spotifyService);
    
    if (isset($_GET['random']) && $_GET['random'] === 'true') {
        // Get random playlists from a mix of moods
        $moods = ['happy', 'chill', 'sad', 'focus', 'energetic', 'romantic', 'party', 'sleep'];
        $randomMoods = array_rand(array_flip($moods), 3); // Get 3 random moods
        $playlists = [];
        
        foreach ($randomMoods as $mood) {
            $moodPlaylists = $playlistService->getPlaylistsForMood($mood);
            $playlists = array_merge($playlists, $moodPlaylists);
        }
        
        // Shuffle and limit to 8 playlists
        shuffle($playlists);
        $playlists = array_slice($playlists, 0, 8);
    } else {
        $mood = $_GET['mood'] ?? null;
        
        if (!$mood) {
            throw new Exception('Mood parameter is required');
        }
        
        $playlists = $playlistService->getPlaylistsForMood($mood);
    }
    
    echo json_encode([
        'error' => false,
        'data' => $playlists
    ]);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'error' => true,
        'message' => $e->getMessage()
    ]);
}
