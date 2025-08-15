<?php

namespace App;

class PlaylistService {
    private function debug($message, $data = null) {
        if (Config::get('APP_DEBUG', false)) {
            error_log("[PlaylistService] $message" . ($data ? ': ' . json_encode($data) : ''));
        }
    }
    private $spotifyService;
    private $moodMappings = [
        'happy' => ['happy', 'feel good', 'good vibes'],
        'chill' => ['chill', 'lofi', 'laid back', 'relax'],
        'sad' => ['sad', 'heartbreak', 'emo'],
        'focus' => ['focus', 'deep work', 'concentration', 'instrumental'],
        'energetic' => ['workout', 'hype', 'power', 'pump'],
        'romantic' => ['romantic', 'love', 'date night'],
        'party' => ['party', 'dance', 'club'],
        'sleep' => ['sleep', 'calm', 'ambient']
    ];
    
    public function __construct(SpotifyService $spotifyService) {
        $this->spotifyService = $spotifyService;
    }
    
    public function getPlaylistsForMood($mood) {
        if (!is_string($mood)) {
            throw new \Exception('Mood must be a string');
        }

        $mood = strtolower(trim($mood));
        
        if (!isset($this->moodMappings[$mood])) {
            throw new \Exception('Invalid mood specified. Available moods: ' . implode(', ', array_keys($this->moodMappings)));
        }
        
        $queries = $this->moodMappings[$mood];
        $playlists = [];
        $seenIds = [];
        $errors = [];
        
        foreach ($queries as $query) {
            try {
                $results = $this->spotifyService->searchPlaylists($query);
                
                if (!is_array($results) || !isset($results['playlists']) || !isset($results['playlists']['items'])) {
                    $errors[] = "Invalid response format for query: $query";
                    continue;
                }
                
                foreach ($results['playlists']['items'] as $playlist) {
                    if (!isset($playlist['id'])) {
                        continue;
                    }
                    
                    if (!isset($seenIds[$playlist['id']])) {
                        try {
                            $playlists[] = $this->normalizePlaylist($playlist);
                            $seenIds[$playlist['id']] = true;
                        } catch (\Exception $e) {
                            $errors[] = "Error processing playlist: " . $e->getMessage();
                        }
                    }
                }
            } catch (\Exception $e) {
                $errors[] = "Error searching for query '$query': " . $e->getMessage();
            }
        }
        
        if (empty($playlists) && !empty($errors)) {
            throw new \Exception('Failed to fetch playlists: ' . implode('; ', $errors));
        }
        
        return array_slice($playlists, 0, 24); // Return max 24 playlists
    }
    
    private function normalizePlaylist($playlist) {
        if (!is_array($playlist)) {
            throw new \Exception('Invalid playlist data received');
        }

        // Ensure all required fields exist with fallbacks
        return [
            'id' => $playlist['id'] ?? 'unknown',
            'name' => $playlist['name'] ?? 'Untitled Playlist',
            'description' => $playlist['description'] ?? '',
            'image' => isset($playlist['images'][0]) ? ($playlist['images'][0]['url'] ?? null) : null,
            'owner' => isset($playlist['owner']) ? ($playlist['owner']['display_name'] ?? 'Unknown Artist') : 'Unknown Artist',
            'followers' => isset($playlist['followers']) ? ($playlist['followers']['total'] ?? 0) : 0,
            'external_url' => isset($playlist['external_urls']['spotify']) ? $playlist['external_urls']['spotify'] : '#'
        ];
    }
}
