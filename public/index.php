<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Moodify - Perfect Playlist</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="/spotify/public/assets/css/styles.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://accounts.google.com/gsi/client" async></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'system-ui', 'sans-serif'],
                    },
                }
            }
        }
    </script>
    <script>
        // Check for saved dark mode preference
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }
    </script>
</head>
<body class="bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800 min-h-screen font-sans text-gray-900 dark:text-gray-100 transition-colors duration-200">
    <nav class="backdrop-blur-md bg-white/80 dark:bg-gray-900/80 sticky top-0 z-50 border-b border-gray-200/80 dark:border-gray-700/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex-shrink-0 flex items-center">
                    <svg class="w-8 h-8 text-[#1ed760]" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm4.9 14.1c-.19.3-.6.4-.9.2-2.49-1.5-5.62-1.9-9.3-1-1.29.16-1.96-1.11-1.38-2.17.41-.75 1.2-1.14 2.02-1.05 4.31.97 7.89 1.47 10.76 3.19.3.19.4.6.2.9zm1.31-2.89c-.24.35-.69.48-1.08.31-2.85-1.75-7.19-2.26-10.55-1.23-.44.13-.91-.12-1.05-.56-.13-.44.12-.91.56-1.05 3.84-1.17 8.6-.61 11.89 1.45.39.24.5.74.27 1.09zm.11-3c-3.41-2.02-9.03-2.21-12.27-1.22-.55.17-1.13-.14-1.3-.69-.17-.55.14-1.13.69-1.3 3.75-1.14 9.99-.91 13.93 1.41.51.31.68.98.37 1.48-.31.51-.98.68-1.48.37z"/>
                    </svg>
                    <h1 class="ml-2 text-xl font-bold bg-gradient-to-r from-[#1ed760] to-[#1DB954] bg-clip-text text-transparent">
                        Moodify
                    </h1>
                </div>
                <div class="flex items-center gap-4">
                    <!-- Mobile menu button -->
                    <button id="mobileMenuButton" class="md:hidden p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors duration-200">
                        <svg class="w-6 h-6 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path class="menu-open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            <path class="menu-close hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                    <!-- Dark mode toggle -->
                    <button id="darkModeToggle" class="hidden md:block p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors duration-200">
                        <!-- Sun icon -->
                        <svg class="w-6 h-6 hidden dark:block text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <!-- Moon icon -->
                        <svg class="w-6 h-6 block dark:hidden text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                        </svg>
                    </button>
                    <!-- Google Sign-in button (desktop) -->
                    <div id="signInContainer" class="hidden md:block">
                        <div id="g_id_onload"
                             data-client_id="70588841910-3f9en8h6578c395tcc19tigkhc9m5huq.apps.googleusercontent.com"
                             data-context="signin"
                             data-ux_mode="popup"
                             data-callback="handleCredentialResponse"
                             data-auto_prompt="false">
                        </div>
                        <div class="g_id_signin"
                             data-type="standard"
                             data-shape="pill"
                             data-theme="outline"
                             data-text="signin_with"
                             data-size="large"
                             data-logo_alignment="left">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Mobile menu panel -->
        <div id="mobileMenu" class="md:hidden h-0 overflow-hidden transition-all duration-300 ease-in-out">
            <div class="px-4 py-3 space-y-4 bg-white/95 dark:bg-gray-900/95 backdrop-blur-md border-t border-gray-200/80 dark:border-gray-700/80">
                <!-- Mobile menu items -->
                <div class="grid gap-3">
                    <!-- Dark mode toggle in mobile menu -->
                    <button id="darkModeToggleMobile" class="flex items-center justify-between w-full p-3 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors duration-200">
                        <span class="text-sm font-medium">Dark Mode</span>
                        <div class="flex items-center">
                            <svg class="w-5 h-5 hidden dark:block text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            <svg class="w-5 h-5 block dark:hidden text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                            </svg>
                        </div>
                    </button>

                    <!-- Google Sign-in button (mobile) -->
                    <div class="p-3">
                        <div class="w-full" id="signInContainerMobile">
                            <div id="g_id_onload_mobile"
                                 data-client_id="70588841910-3f9en8h6578c395tcc19tigkhc9m5huq.apps.googleusercontent.com"
                                 data-context="signin"
                                 data-ux_mode="popup"
                                 data-callback="handleCredentialResponse"
                                 data-auto_prompt="false">
                            </div>
                            <div class="g_id_signin"
                                 data-type="standard"
                                 data-shape="rectangular"
                                 data-theme="outline"
                                 data-text="signin_with"
                                 data-size="large"
                                 data-width="100%"
                                 data-logo_alignment="left">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="text-center mb-16 space-y-4 px-4">
            <h2 class="text-4xl sm:text-5xl font-bold text-gray-900 dark:text-white tracking-tight leading-tight">
                Pick your mood
            </h2>
            <p class="text-lg sm:text-xl text-gray-600 dark:text-gray-300 max-w-2xl mx-auto">
                We'll curate the perfect playlist collection to match your vibe
            </p>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-16 max-w-4xl mx-auto">
            <button class="mood-btn group" data-mood="happy">
                <span class="relative z-10 inline-flex items-center gap-2">
                    <span class="text-xl">😊</span>
                    <span>Happy</span>
                </span>
            </button>
            <button class="mood-btn group" data-mood="chill">
                <span class="relative z-10 inline-flex items-center gap-2">
                    <span class="text-xl">😌</span>
                    <span>Chill</span>
                </span>
            </button>
            <button class="mood-btn group" data-mood="sad">
                <span class="relative z-10 inline-flex items-center gap-2">
                    <span class="text-xl">😢</span>
                    <span>Sad</span>
                </span>
            </button>
            <button class="mood-btn group" data-mood="focus">
                <span class="relative z-10 inline-flex items-center gap-2">
                    <span class="text-xl">🎯</span>
                    <span>Focus</span>
                </span>
            </button>
            <button class="mood-btn group" data-mood="energetic">
                <span class="relative z-10 inline-flex items-center gap-2">
                    <span class="text-xl">⚡️</span>
                    <span>Energetic</span>
                </span>
            </button>
            <button class="mood-btn group" data-mood="romantic">
                <span class="relative z-10 inline-flex items-center gap-2">
                    <span class="text-xl">💝</span>
                    <span>Romantic</span>
                </span>
            </button>
            <button class="mood-btn group" data-mood="party">
                <span class="relative z-10 inline-flex items-center gap-2">
                    <span class="text-xl">🎉</span>
                    <span>Party</span>
                </span>
            </button>
            <button class="mood-btn group" data-mood="sleep">
                <span class="relative z-10 inline-flex items-center gap-2">
                    <span class="text-xl">😴</span>
                    <span>Sleep</span>
                </span>
            </button>
        </div>

        <div id="loading" class="hidden">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Loading skeletons -->
                <div class="animate-pulse bg-white/80 backdrop-blur-sm rounded-3xl p-6 shadow-xl shadow-gray-200/50" aria-hidden="true">
                    <div class="bg-gray-200 aspect-square rounded-2xl mb-6"></div>
                    <div class="space-y-3">
                        <div class="h-4 bg-gray-200 rounded-full w-3/4"></div>
                        <div class="h-4 bg-gray-200 rounded-full w-1/2"></div>
                    </div>
                </div>
                <!-- Repeat skeleton 3 more times for visual balance -->
                <div class="animate-pulse bg-white/80 backdrop-blur-sm rounded-3xl p-6 shadow-xl shadow-gray-200/50" aria-hidden="true">
                    <div class="bg-gray-200 aspect-square rounded-2xl mb-6"></div>
                    <div class="space-y-3">
                        <div class="h-4 bg-gray-200 rounded-full w-3/4"></div>
                        <div class="h-4 bg-gray-200 rounded-full w-1/2"></div>
                    </div>
                </div>
                <div class="animate-pulse bg-white/80 backdrop-blur-sm rounded-3xl p-6 shadow-xl shadow-gray-200/50" aria-hidden="true">
                    <div class="bg-gray-200 aspect-square rounded-2xl mb-6"></div>
                    <div class="space-y-3">
                        <div class="h-4 bg-gray-200 rounded-full w-3/4"></div>
                        <div class="h-4 bg-gray-200 rounded-full w-1/2"></div>
                    </div>
                </div>
                <div class="animate-pulse bg-white/80 backdrop-blur-sm rounded-3xl p-6 shadow-xl shadow-gray-200/50" aria-hidden="true">
                    <div class="bg-gray-200 aspect-square rounded-2xl mb-6"></div>
                    <div class="space-y-3">
                        <div class="h-4 bg-gray-200 rounded-full w-3/4"></div>
                        <div class="h-4 bg-gray-200 rounded-full w-1/2"></div>
                    </div>
                </div>
            </div>
        </div>

        <div id="error" class="hidden max-w-2xl mx-auto">
            <div class="bg-red-50 border border-red-200 p-6 rounded-2xl shadow-lg shadow-red-100">
                <div class="flex items-center space-x-4">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-base font-medium text-red-800" id="error-message"></p>
                    </div>
                </div>
            </div>
        </div>

        <div id="playlists" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8"></div>

        <div id="empty" class="hidden text-center py-16">
            <div class="w-48 h-48 mx-auto bg-gradient-to-br from-gray-100 to-gray-50 rounded-full mb-8 flex items-center justify-center">
                <svg class="w-24 h-24 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"/>
                </svg>
            </div>
            <h3 class="text-2xl font-semibold text-gray-900 mb-2">No playlists found</h3>
            <p class="text-gray-500 text-lg">Try selecting a different mood to discover more music</p>
        </div>
    </main>

    <script src="/spotify/public/assets/js/app.js"></script>
</body>
</html>
