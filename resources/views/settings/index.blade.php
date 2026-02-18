@extends('layouts.app')

@section('title', 'Paramètres système')

@section('content')
<div class="flex-1 overflow-y-auto bg-gray-50">

    {{-- Header --}}
    <div class="bg-white border-b border-gray-200 px-6 py-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-lg font-semibold text-gray-900">Paramètres système</h1>
                    <p class="text-xs text-gray-500">Configuration, connexions et logs d'erreur</p>
                </div>
            </div>
            <button onclick="testAll()" id="btn-test-all"
                    class="flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Tout tester
            </button>
        </div>
    </div>

    <div class="p-6 max-w-5xl mx-auto space-y-6">

        {{-- ─── Cartes de statut ─────────────────────────────── --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            {{-- Chatwoot --}}
            <div class="bg-white rounded-xl border border-gray-200 p-5" id="card-chatwoot">
                <div class="flex items-start justify-between mb-3">
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 bg-indigo-50 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-900">Chatwoot</p>
                            <p class="text-xs text-gray-400">API Support Client</p>
                        </div>
                    </div>
                    <span id="status-chatwoot" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-medium bg-gray-100 text-gray-500">
                        <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span> Non testé
                    </span>
                </div>
                <p id="detail-chatwoot" class="text-xs text-gray-400 min-h-[16px]">{{ $config['chatwoot']['base_url'] }}</p>
                <button onclick="testConnection('chatwoot')"
                        class="mt-3 w-full py-1.5 text-xs font-medium text-indigo-600 bg-indigo-50 hover:bg-indigo-100 rounded-lg transition">
                    Tester la connexion
                </button>
            </div>

            {{-- Twilio --}}
            <div class="bg-white rounded-xl border border-gray-200 p-5" id="card-twilio">
                <div class="flex items-start justify-between mb-3">
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 bg-red-50 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-900">Twilio</p>
                            <p class="text-xs text-gray-400">API WhatsApp</p>
                        </div>
                    </div>
                    <span id="status-twilio" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-medium bg-gray-100 text-gray-500">
                        <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span> Non testé
                    </span>
                </div>
                <p id="detail-twilio" class="text-xs text-gray-400 min-h-[16px]">{{ $config['twilio']['whatsapp_from'] ?: 'Non configuré' }}</p>
                <button onclick="testConnection('twilio')"
                        class="mt-3 w-full py-1.5 text-xs font-medium text-red-600 bg-red-50 hover:bg-red-100 rounded-lg transition">
                    Tester la connexion
                </button>
            </div>

            {{-- Base de données --}}
            <div class="bg-white rounded-xl border border-gray-200 p-5" id="card-database">
                <div class="flex items-start justify-between mb-3">
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 bg-green-50 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-900">Base de données</p>
                            <p class="text-xs text-gray-400">{{ strtoupper($config['database']['connection']) }}</p>
                        </div>
                    </div>
                    <span id="status-database" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-medium bg-gray-100 text-gray-500">
                        <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span> Non testé
                    </span>
                </div>
                <p id="detail-database" class="text-xs text-gray-400 min-h-[16px]">{{ $config['database']['username'] }}@{{ $config['database']['host'] }}/{{ $config['database']['database'] }}</p>
                <button onclick="testConnection('database')"
                        class="mt-3 w-full py-1.5 text-xs font-medium text-green-600 bg-green-50 hover:bg-green-100 rounded-lg transition">
                    Tester la connexion
                </button>
            </div>
        </div>

        {{-- ─── Onglets de configuration ─────────────────────── --}}
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            {{-- Tab header --}}
            <div class="border-b border-gray-200 flex">
                <button onclick="switchTab('chatwoot')" id="tab-chatwoot"
                        class="tab-btn active-tab px-5 py-3 text-sm font-medium border-b-2 border-indigo-600 text-indigo-600 transition">
                    Chatwoot
                </button>
                <button onclick="switchTab('twilio')" id="tab-twilio"
                        class="tab-btn px-5 py-3 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 transition">
                    Twilio
                </button>
                <button onclick="switchTab('app')" id="tab-app"
                        class="tab-btn px-5 py-3 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 transition">
                    Application
                </button>
                <button onclick="switchTab('logs')" id="tab-logs"
                        class="tab-btn px-5 py-3 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 transition">
                    Logs
                </button>
            </div>

            {{-- ── Tab Chatwoot ────────────────────────────────── --}}
            <div id="panel-chatwoot" class="tab-panel p-6">
                <p class="text-xs text-gray-500 mb-5">Connexion à votre instance Chatwoot. Après modification, la connexion doit être retestée.</p>
                <form onsubmit="saveSection(event, 'chatwoot')" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-xs font-medium text-gray-700 mb-1">URL de l'instance Chatwoot</label>
                            <input type="url" name="CHATWOOT_BASE_URL" value="{{ $config['chatwoot']['base_url'] }}"
                                   class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                   placeholder="https://support.example.com">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Account ID</label>
                            <input type="text" name="CHATWOOT_ACCOUNT_ID" value="{{ $config['chatwoot']['account_id'] }}"
                                   class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                   placeholder="1">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">WhatsApp Inbox ID</label>
                            <input type="text" name="CHATWOOT_WHATSAPP_INBOX_ID" value="{{ $config['chatwoot']['whatsapp_inbox_id'] }}"
                                   class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                   placeholder="ID de l'inbox WhatsApp">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-medium text-gray-700 mb-1">API Token</label>
                            <div class="relative">
                                <input type="password" name="CHATWOOT_API_TOKEN" value="{{ $config['chatwoot']['api_token'] }}"
                                       id="field-chatwoot-token"
                                       class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent pr-10"
                                       placeholder="user_access_token">
                                <button type="button" onclick="toggleReveal('field-chatwoot-token')"
                                        class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </button>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Webhook Secret</label>
                            <div class="relative">
                                <input type="password" name="CHATWOOT_WEBHOOK_SECRET" value="{{ $config['chatwoot']['webhook_secret'] }}"
                                       id="field-chatwoot-webhook"
                                       class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent pr-10"
                                       placeholder="Secret webhook">
                                <button type="button" onclick="toggleReveal('field-chatwoot-webhook')"
                                        class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </button>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Intervalle de polling (ms)</label>
                            <input type="number" name="CHATWOOT_POLLING_INTERVAL" value="{{ $config['chatwoot']['polling_interval'] }}"
                                   class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                   min="1000" max="60000" step="1000">
                        </div>
                    </div>
                    <div class="flex justify-end pt-2">
                        <button type="submit"
                                class="px-5 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition flex items-center gap-2">
                            <svg class="w-4 h-4 save-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span class="save-label">Enregistrer</span>
                        </button>
                    </div>
                </form>
            </div>

            {{-- ── Tab Twilio ──────────────────────────────────── --}}
            <div id="panel-twilio" class="tab-panel hidden p-6">
                <p class="text-xs text-gray-500 mb-5">Identifiants Twilio pour l'envoi de messages WhatsApp.</p>
                <form onsubmit="saveSection(event, 'twilio')" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Account SID</label>
                            <input type="text" name="TWILIO_SID" value="{{ $config['twilio']['sid'] }}"
                                   class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                   placeholder="ACxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Numéro WhatsApp (From)</label>
                            <input type="text" name="TWILIO_WHATSAPP_FROM" value="{{ $config['twilio']['whatsapp_from'] }}"
                                   class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                   placeholder="whatsapp:+14155238886">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-medium text-gray-700 mb-1">Auth Token</label>
                            <div class="relative">
                                <input type="password" name="TWILIO_AUTH_TOKEN" value="{{ $config['twilio']['auth_token'] }}"
                                       id="field-twilio-token"
                                       class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent pr-10"
                                       placeholder="Auth token Twilio">
                                <button type="button" onclick="toggleReveal('field-twilio-token')"
                                        class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-end pt-2">
                        <button type="submit"
                                class="px-5 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition flex items-center gap-2">
                            <svg class="w-4 h-4 save-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span class="save-label">Enregistrer</span>
                        </button>
                    </div>
                </form>
            </div>

            {{-- ── Tab Application ─────────────────────────────── --}}
            <div id="panel-app" class="tab-panel hidden p-6">
                <p class="text-xs text-gray-500 mb-5">Paramètres généraux de l'application.</p>
                <form onsubmit="saveSection(event, 'app')" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Nom de l'application</label>
                            <input type="text" name="APP_NAME" value="{{ $config['app']['name'] }}"
                                   class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">URL de l'application</label>
                            <input type="url" name="APP_URL" value="{{ $config['app']['url'] }}"
                                   class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Environnement</label>
                            <select name="APP_ENV"
                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                <option value="local" {{ $config['app']['env'] === 'local' ? 'selected' : '' }}>Local (développement)</option>
                                <option value="production" {{ $config['app']['env'] === 'production' ? 'selected' : '' }}>Production</option>
                                <option value="staging" {{ $config['app']['env'] === 'staging' ? 'selected' : '' }}>Staging</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Fuseau horaire</label>
                            <input type="text" name="APP_TIMEZONE" value="{{ $config['app']['timezone'] }}"
                                   class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                   placeholder="Africa/Abidjan">
                        </div>
                        <div class="flex items-center gap-3">
                            <label class="text-xs font-medium text-gray-700">Mode debug</label>
                            <button type="button" onclick="toggleDebug(this)"
                                    data-on="{{ $config['app']['debug'] ? 'true' : 'false' }}"
                                    class="debug-toggle relative w-10 h-5 rounded-full transition-colors {{ $config['app']['debug'] ? 'bg-red-500' : 'bg-gray-300' }}">
                                <span class="absolute top-0.5 h-4 w-4 rounded-full bg-white shadow transition-transform {{ $config['app']['debug'] ? 'translate-x-5' : 'translate-x-0.5' }}"></span>
                            </button>
                            <input type="hidden" name="APP_DEBUG" id="app-debug-value" value="{{ $config['app']['debug'] ? 'true' : 'false' }}">
                            <span id="debug-label" class="text-xs {{ $config['app']['debug'] ? 'text-red-500 font-medium' : 'text-gray-400' }}">
                                {{ $config['app']['debug'] ? 'Activé (attention !)' : 'Désactivé' }}
                            </span>
                        </div>
                    </div>
                    <div class="flex justify-end pt-2">
                        <button type="submit"
                                class="px-5 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition flex items-center gap-2">
                            <svg class="w-4 h-4 save-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span class="save-label">Enregistrer</span>
                        </button>
                    </div>
                </form>
            </div>

            {{-- ── Tab Logs ─────────────────────────────────────── --}}
            <div id="panel-logs" class="tab-panel hidden p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-sm font-medium text-gray-900">Journal d'erreurs Laravel</p>
                        <p class="text-xs text-gray-400 mt-0.5" id="log-size-label"></p>
                    </div>
                    <div class="flex gap-2">
                        <button onclick="loadLogs()"
                                class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            Rafraîchir
                        </button>
                        <button onclick="confirmClearLogs()"
                                class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-red-600 bg-red-50 hover:bg-red-100 rounded-lg transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Vider les logs
                        </button>
                    </div>
                </div>
                <div id="log-loading" class="hidden text-center py-8 text-gray-400 text-sm">Chargement...</div>
                <pre id="log-content"
                     class="bg-gray-900 text-gray-100 rounded-lg p-4 text-[11px] font-mono leading-relaxed overflow-auto max-h-[500px] whitespace-pre-wrap break-all">
<span class="text-gray-500 italic">Cliquez sur "Rafraîchir" pour charger les logs.</span></pre>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
const TOKEN = document.querySelector('meta[name="csrf-token"]')?.content;

// ═══ Onglets ═══════════════════════════════════════════
function switchTab(name) {
    document.querySelectorAll('.tab-panel').forEach(p => p.classList.add('hidden'));
    document.querySelectorAll('.tab-btn').forEach(b => {
        b.classList.remove('active-tab', 'border-indigo-600', 'text-indigo-600');
        b.classList.add('border-transparent', 'text-gray-500');
    });

    document.getElementById('panel-' + name).classList.remove('hidden');
    const btn = document.getElementById('tab-' + name);
    btn.classList.add('active-tab', 'border-indigo-600', 'text-indigo-600');
    btn.classList.remove('border-transparent', 'text-gray-500');

    if (name === 'logs') loadLogs();
}

// ═══ Tester les connexions ══════════════════════════════
async function testConnection(service) {
    const statusEl  = document.getElementById('status-' + service);
    const detailEl  = document.getElementById('detail-' + service);

    // État "en cours"
    statusEl.innerHTML = `<svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg> Test...`;
    statusEl.className = 'inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-medium bg-gray-100 text-gray-500';

    try {
        const r    = await fetch(`/ajax/settings/test-${service}`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': TOKEN, 'Accept': 'application/json' },
        });
        const data = await r.json();

        if (data.ok) {
            statusEl.innerHTML = `<span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> OK`;
            statusEl.className = 'inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-medium bg-green-50 text-green-700';
            detailEl.textContent = `Connexion établie — ${data.latency_ms}ms`;
            detailEl.className = 'text-xs text-green-600 min-h-[16px]';
        } else {
            statusEl.innerHTML = `<span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Erreur`;
            statusEl.className = 'inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-medium bg-red-50 text-red-700';
            detailEl.textContent = data.error || 'Erreur inconnue';
            detailEl.className = 'text-xs text-red-500 min-h-[16px]';
        }
    } catch (e) {
        statusEl.innerHTML = `<span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Erreur`;
        statusEl.className = 'inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-medium bg-red-50 text-red-700';
        detailEl.textContent = 'Erreur réseau';
        detailEl.className = 'text-xs text-red-500 min-h-[16px]';
    }
}

async function testAll() {
    const btn = document.getElementById('btn-test-all');
    btn.disabled = true;
    btn.classList.add('opacity-60');
    await Promise.all([
        testConnection('chatwoot'),
        testConnection('twilio'),
        testConnection('database'),
    ]);
    btn.disabled = false;
    btn.classList.remove('opacity-60');
}

// ═══ Enregistrer une section ════════════════════════════
async function saveSection(e, section) {
    e.preventDefault();
    const form   = e.target;
    const btn    = form.querySelector('button[type="submit"]');
    const label  = btn.querySelector('.save-label');
    const icon   = btn.querySelector('.save-icon');

    btn.disabled = true;
    label.textContent = 'Enregistrement...';

    const data = {};
    new FormData(form).forEach((v, k) => { if (k !== '_token') data[k] = v; });

    try {
        const r = await fetch('/ajax/settings/update', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': TOKEN,
                'Accept': 'application/json',
            },
            body: JSON.stringify(data),
        });
        const res = await r.json();

        if (res.ok) {
            label.textContent = 'Enregistré !';
            icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>';
            btn.classList.replace('bg-indigo-600', 'bg-green-600');
            showToast('Configuration enregistrée avec succès', 'success');
        } else {
            label.textContent = 'Erreur';
            showToast('Erreur : ' + (res.error || 'Inconnue'), 'error');
        }
    } catch (ex) {
        label.textContent = 'Erreur';
        showToast('Erreur réseau', 'error');
    }

    setTimeout(() => {
        btn.disabled = false;
        label.textContent = 'Enregistrer';
        btn.classList.replace('bg-green-600', 'bg-indigo-600');
    }, 2000);
}

// ═══ Toggle reveal password ═════════════════════════════
function toggleReveal(fieldId) {
    const input = document.getElementById(fieldId);
    input.type = input.type === 'password' ? 'text' : 'password';
}

// ═══ Toggle debug ═══════════════════════════════════════
function toggleDebug(btn) {
    const isOn   = btn.dataset.on === 'true';
    const newVal = !isOn;
    btn.dataset.on = String(newVal);
    document.getElementById('app-debug-value').value = String(newVal);
    const label = document.getElementById('debug-label');

    if (newVal) {
        btn.classList.replace('bg-gray-300', 'bg-red-500');
        btn.querySelector('span').classList.replace('translate-x-0.5', 'translate-x-5');
        label.textContent = 'Activé (attention !)';
        label.className = 'text-xs text-red-500 font-medium';
    } else {
        btn.classList.replace('bg-red-500', 'bg-gray-300');
        btn.querySelector('span').classList.replace('translate-x-5', 'translate-x-0.5');
        label.textContent = 'Désactivé';
        label.className = 'text-xs text-gray-400';
    }
}

// ═══ Logs ═══════════════════════════════════════════════
async function loadLogs() {
    const pre     = document.getElementById('log-content');
    const loading = document.getElementById('log-loading');
    const sizeEl  = document.getElementById('log-size-label');

    pre.classList.add('opacity-40');
    loading.classList.remove('hidden');

    try {
        const r    = await fetch('/ajax/settings/logs?lines=200', {
            headers: { 'X-CSRF-TOKEN': TOKEN, 'Accept': 'application/json' }
        });
        const data = await r.json();
        const lines = data.lines || [];
        const sizeKb = ((data.size || 0) / 1024).toFixed(1);

        sizeEl.textContent = `${lines.length} lignes — ${sizeKb} Ko`;

        if (lines.length === 0) {
            pre.textContent = 'Aucune entrée dans les logs.';
        } else {
            pre.textContent = lines.join('\n');
            pre.scrollTop = pre.scrollHeight;
        }
    } catch (e) {
        pre.textContent = 'Erreur lors du chargement des logs.';
    }

    loading.classList.add('hidden');
    pre.classList.remove('opacity-40');
}

async function confirmClearLogs() {
    if (!confirm('Vider tous les logs ? Cette action est irréversible.')) return;

    try {
        const r = await fetch('/ajax/settings/logs', {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': TOKEN, 'Accept': 'application/json' },
        });
        const data = await r.json();

        if (data.ok) {
            document.getElementById('log-content').textContent = 'Logs vidés.';
            document.getElementById('log-size-label').textContent = '0 lignes — 0 Ko';
            showToast('Logs vidés avec succès', 'success');
        }
    } catch (e) {
        showToast('Erreur lors de la suppression des logs', 'error');
    }
}

// ═══ Toast ══════════════════════════════════════════════
function showToast(message, type = 'success') {
    const container = document.getElementById('msg-toasts');
    if (!container) return;

    const toast = document.createElement('div');
    const colors = type === 'success'
        ? 'bg-green-50 border-green-200 text-green-800'
        : 'bg-red-50 border-red-200 text-red-800';

    toast.className = `${colors} border rounded-xl shadow-lg px-4 py-3 flex items-center gap-3 text-sm font-medium animate-slide-in`;
    toast.innerHTML = `
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            ${type === 'success'
                ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>'
                : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>'}
        </svg>
        ${escH(message)}`;
    container.appendChild(toast);
    setTimeout(() => { toast.style.opacity = '0'; setTimeout(() => toast.remove(), 300); }, 4000);
}

function escH(t) { const d = document.createElement('div'); d.textContent = t; return d.innerHTML; }
</script>
@endpush
