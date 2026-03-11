<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Connexion - SportCash</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            body { font-family: 'Inter', sans-serif; }
        </style>
    </head>
    <body class="antialiased">
        <div class="min-h-screen flex">

            {{-- ── Colonne gauche : branding ── --}}
            <div class="hidden lg:flex lg:w-1/2 bg-green-900 flex-col items-center justify-center px-12 relative overflow-hidden">

                {{-- Cercles décoratifs --}}
                <div class="absolute -top-24 -left-24 w-72 h-72 bg-orange-600 rounded-full opacity-40"></div>
                <div class="absolute bottom-0 right-0 w-96 h-96 bg-orange-600 rounded-full opacity-20 translate-x-1/3 translate-y-1/3"></div>
                <div class="absolute top-1/2 left-0 w-40 h-40 bg-orange-500 rounded-full opacity-20 -translate-x-1/2"></div>

                {{-- Contenu --}}
                <div class="relative z-10 max-w-md text-center">

                    {{-- Logo --}}
                    <div class="flex justify-center mb-10">
                        <div class="bg-white rounded-2xl px-6 py-4 shadow-lg inline-block">
                            <img src="{{ asset('images/logo_new.png') }}" alt="SporSportCash" class="h-14 w-auto object-contain">
                        </div>
                    </div>

                    {{-- Titre --}}
                    <h1 class="text-3xl font-bold text-white mb-3 leading-tight">Support Client</h1>
                    <p class="text-green-100 text-base leading-relaxed mb-10">
                        Plateforme de gestion des communications clients via WhatsApp.
                    </p>

                    {{-- Badges fonctionnalités --}}
                    <div class="flex flex-col gap-3">

                        <div class="flex items-center gap-4 bg-white/10 backdrop-blur-sm border border-white/20 rounded-xl px-5 py-3.5 text-left">
                            <div class="w-9 h-9 bg-white/20 rounded-lg flex items-center justify-center flex-shrink-0">
                                {{-- WhatsApp icon --}}
                                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-white font-semibold text-sm">Support WhatsApp</p>
                                <p class="text-green-200 text-xs">Gestion des conversations clients</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-4 bg-white/10 backdrop-blur-sm border border-white/20 rounded-xl px-5 py-3.5 text-left">
                            <div class="w-9 h-9 bg-white/20 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-white font-semibold text-sm">Temps réel</p>
                                <p class="text-green-200 text-xs">Suivi instantané des échanges</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-4 bg-white/10 backdrop-blur-sm border border-white/20 rounded-xl px-5 py-3.5 text-left">
                            <div class="w-9 h-9 bg-white/20 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-white font-semibold text-sm">Statistiques</p>
                                <p class="text-green-200 text-xs">Tableaux de bord et rapports</p>
                            </div>
                        </div>

                    </div>

                </div>
            </div>

            {{-- ── Colonne droite : formulaire ── --}}
            <div class="flex-1 flex flex-col items-center justify-center bg-gray-50 px-6 py-12">

                {{-- Logo visible uniquement sur mobile --}}
                <div class="lg:hidden flex justify-center mb-8">
                    <img src="{{ asset('images/logo_new.png') }}" alt="SporSportCash" class="h-14 w-auto object-contain">
                </div>

                <div class="w-full max-w-md">
                    @yield('content')
                </div>

                <p class="text-center text-xs text-gray-400 mt-8">© 2026 SportCash · Tous droits réservés</p>
            </div>

        </div>
        @stack('scripts')
    </body>
</html>
