<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Connexion - YesWeChange Support</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            body { font-family: 'Inter', sans-serif; }
        </style>
    </head>
    <body class="antialiased">
        <div class="min-h-screen flex">

            {{-- Panneau gauche : branding --}}
            <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-indigo-600 via-purple-600 to-indigo-800 relative overflow-hidden">
                <div class="absolute inset-0 opacity-10">
                    <div class="absolute top-20 left-20 w-72 h-72 bg-white rounded-full blur-3xl"></div>
                    <div class="absolute bottom-20 right-20 w-96 h-96 bg-white rounded-full blur-3xl"></div>
                </div>

                <div class="relative z-10 flex flex-col justify-center px-16 text-white">
                    <div class="mb-8">
                        <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center mb-6">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                        </div>
                        <h1 class="text-4xl font-bold leading-tight mb-4">
                            YesWeChange<br>
                            <span class="text-white/80 font-normal">Agent Dashboard</span>
                        </h1>
                        <p class="text-lg text-white/70 max-w-md leading-relaxed">
                            Gerez vos conversations WhatsApp, suivez les performances de votre equipe et offrez un support client exceptionnel.
                        </p>
                    </div>

                    <div class="space-y-4 mt-8">
                        <div class="flex items-center gap-3 text-white/80">
                            <div class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <span class="text-sm">Conversations en temps reel</span>
                        </div>
                        <div class="flex items-center gap-3 text-white/80">
                            <div class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <span class="text-sm">Integration WhatsApp via Twilio</span>
                        </div>
                        <div class="flex items-center gap-3 text-white/80">
                            <div class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <span class="text-sm">Tableau de bord et statistiques</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Panneau droit : formulaire --}}
            <div class="w-full lg:w-1/2 flex items-center justify-center bg-gray-50 px-6 py-12">
                <div class="w-full max-w-md">

                    {{-- Logo mobile uniquement --}}
                    <div class="lg:hidden text-center mb-8">
                        <div class="inline-flex w-12 h-12 bg-indigo-600 rounded-xl items-center justify-center mb-4">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                        </div>
                        <h1 class="text-xl font-bold text-gray-900">YesWeChange Support</h1>
                    </div>

                    {{ $slot }}
                </div>
            </div>

        </div>
    </body>
</html>
