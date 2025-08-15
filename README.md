# Moodify - Mood-based Spotify Playlist Finder

A simple web application that helps users discover Spotify playlists based on their current mood. Built with PHP, vanilla JavaScript, and Tailwind CSS.

## Features

- Select from 8 different moods (Happy, Chill, Sad, Focus, Energetic, Romantic, Party, Sleep)
- View curated Spotify playlists matching your mood
- Clean, modern UI with responsive design
- Rate limiting to prevent API abuse
- Token caching for optimal Spotify API usage

## Requirements

- PHP 7.4 or higher
- Composer
- XAMPP/Apache web server
- Spotify API credentials

## Setup Instructions

1. Clone this repository to your XAMPP htdocs folder:

```bash
cd /path/to/xampp/htdocs
git clone https://your-repository-url/moodify.git
```

2. Install PHP dependencies:

```bash
composer install
```

3. Copy the environment file and update with your Spotify API credentials:

```bash
cp .env.example .env
```

4. Configure your environment variables in `.env`:

```
SPOTIFY_CLIENT_ID=your_client_id_here
SPOTIFY_CLIENT_SECRET=your_client_secret_here
APP_ENV=local
APP_DEBUG=true
```

5. Ensure the storage directory is writable:

```bash
chmod -R 777 storage
```

6. Access the application through your web browser:

```
http://localhost/spotify/
```

## Project Structure

```
moodify/
├── public/              # Public-facing files
│   ├── index.php       # Main page
│   ├── api/            # API endpoints
│   └── assets/         # Static assets
├── src/                # PHP backend source
│   ├── Config.php
│   ├── SpotifyService.php
│   └── PlaylistService.php
└── storage/            # Cache & logs
```

## Security Notes

- API credentials are stored securely in .env file
- Rate limiting implemented to prevent abuse
- Token caching to minimize API requests
- Input sanitization for all user inputs

## Browser Support

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)

## License

MIT License
