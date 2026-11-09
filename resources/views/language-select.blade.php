<!DOCTYPE html>
<html lang="de">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Willkommen / Welcome — Vereinsverwaltung</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gray-50 text-gray-900">
        <div class="min-h-screen flex flex-col items-center justify-center px-4">
            <!-- Logo / Titel -->
            <div class="text-center mb-12">
                <h1 class="text-4xl font-bold text-gray-800 mb-3">
                    {{ config('app.name', 'Vereinsverwaltung') }}
                </h1>
                <p class="text-lg text-gray-500">
                    Anleitungen und Tutorials für die Vereinsverwaltung
                </p>
                <p class="text-lg text-gray-500 mt-1">
                    Guides and tutorials for club management
                </p>
            </div>

            <!-- Sprachauswahl -->
            <div class="text-center mb-8">
                <h2 class="text-xl font-semibold text-gray-700 mb-6">
                    Sprache wählen / Choose language
                </h2>

                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <!-- Deutsch -->
                    <a href="{{ url('de/') }}"
                       class="group flex items-center gap-4 px-8 py-6 bg-white border-2 border-gray-200 rounded-xl hover:border-blue-500 hover:shadow-lg transition-all duration-200">
                        <span class="text-4xl">🇩🇪</span>
                        <div class="text-left">
                            <div class="text-lg font-semibold text-gray-800 group-hover:text-blue-600">Deutsch</div>
                            <div class="text-sm text-gray-500">Auf Deutsch fortfahren</div>
                        </div>
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-blue-500 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>

                    <!-- English -->
                    <a href="{{ url('en/') }}"
                       class="group flex items-center gap-4 px-8 py-6 bg-white border-2 border-gray-200 rounded-xl hover:border-blue-500 hover:shadow-lg transition-all duration-200">
                        <span class="text-4xl">🇬🇧</span>
                        <div class="text-left">
                            <div class="text-lg font-semibold text-gray-800 group-hover:text-blue-600">English</div>
                            <div class="text-sm text-gray-500">Continue in English</div>
                        </div>
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-blue-500 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Hinweis -->
            <p class="text-center text-sm text-gray-400 mt-8">
                Die Sprache können Sie später jederzeit in der Navigation ändern.<br>
                You can change the language at any time in the navigation.
            </p>

            <!-- Footer -->
            <footer class="fixed bottom-4 text-center w-full">
                <p class="text-xs text-gray-300">&copy; {{ date('Y') }} {{ config('app.name', 'Vereinsverwaltung') }}</p>
            </footer>
        </div>
    </body>
</html>
