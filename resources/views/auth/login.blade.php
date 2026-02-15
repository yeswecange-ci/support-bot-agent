<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - YesWeCange</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        :root {
            --bg: #f8fafc;
            --card: #ffffff;
            --text: #1e293b;
            --text-secondary: #64748b;
            --text-muted: #94a3b8;
            --border: #e2e8f0;
            --accent: #4f46e5;
            --accent-light: #eef2ff;
            --accent-hover: #4338ca;
            --accent-ring: rgba(79, 70, 229, 0.15);
            --input-bg: #ffffff;
            --error: #ef4444;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
        }

        /* ═══ Layout split ═══ */
        .page {
            display: flex;
            width: 100%;
            min-height: 100vh;
        }

        .branding {
            display: none;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            width: 45%;
            padding: 60px;
            background: var(--accent-light);
            position: relative;
            overflow: hidden;
        }

        .branding::before {
            content: '';
            position: absolute;
            top: -20%;
            right: -20%;
            width: 60%;
            height: 60%;
            border-radius: 50%;
            background: rgba(79, 70, 229, 0.06);
        }

        .branding::after {
            content: '';
            position: absolute;
            bottom: -15%;
            left: -10%;
            width: 50%;
            height: 50%;
            border-radius: 50%;
            background: rgba(79, 70, 229, 0.04);
        }

        .branding-content {
            position: relative;
            z-index: 1;
            text-align: center;
            max-width: 360px;
        }

        .branding-icon {
            width: 72px;
            height: 72px;
            margin: 0 auto 32px;
            background: var(--accent);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .branding-icon svg {
            width: 36px;
            height: 36px;
            color: white;
        }

        .branding h2 {
            font-size: 24px;
            font-weight: 700;
            color: var(--text);
            letter-spacing: -0.025em;
            margin-bottom: 12px;
        }

        .branding p {
            font-size: 15px;
            color: var(--text-secondary);
            line-height: 1.6;
        }

        .branding-features {
            margin-top: 40px;
            text-align: left;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .branding-feature {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 14px;
            color: var(--text-secondary);
        }

        .branding-feature .dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--accent);
            flex-shrink: 0;
            opacity: 0.6;
        }

        /* ═══ Form side ═══ */
        .form-side {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 24px;
        }

        .form-container {
            width: 100%;
            max-width: 400px;
            animation: fadeIn 0.5s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .card {
            background: var(--card);
            border-radius: 20px;
            padding: 44px 36px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04), 0 4px 16px rgba(0, 0, 0, 0.03);
            border: 1px solid var(--border);
        }

        /* ═══ Header ═══ */
        .form-header {
            margin-bottom: 36px;
        }

        .mobile-logo {
            width: 48px;
            height: 48px;
            background: var(--accent);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 24px;
        }

        .mobile-logo svg {
            width: 24px;
            height: 24px;
            color: white;
        }

        .form-header h1 {
            font-size: 22px;
            font-weight: 700;
            letter-spacing: -0.025em;
            color: var(--text);
            margin-bottom: 6px;
        }

        .form-header p {
            font-size: 14px;
            color: var(--text-muted);
        }

        /* ═══ Form ═══ */
        .field {
            margin-bottom: 20px;
        }

        .field label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            color: var(--text-secondary);
            margin-bottom: 6px;
        }

        .field-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .field input[type="email"],
        .field input[type="password"] {
            width: 100%;
            padding: 12px 14px;
            font-size: 14px;
            font-family: inherit;
            color: var(--text);
            background: var(--input-bg);
            border: 1px solid var(--border);
            border-radius: 10px;
            outline: none;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .field input:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px var(--accent-ring);
        }

        .field input::placeholder {
            color: var(--text-muted);
        }

        .forgot-link {
            font-size: 13px;
            color: var(--accent);
            text-decoration: none;
            font-weight: 500;
            transition: opacity 0.2s;
        }

        .forgot-link:hover {
            opacity: 0.8;
        }

        .error-text {
            font-size: 12px;
            color: var(--error);
            margin-top: 6px;
            display: none;
        }

        .error-text.show {
            display: block;
        }

        /* ═══ Checkbox ═══ */
        .remember-row {
            display: flex;
            align-items: center;
            margin: 24px 0 28px;
        }

        .remember-row input[type="checkbox"] {
            width: 16px;
            height: 16px;
            margin-right: 8px;
            accent-color: var(--accent);
            cursor: pointer;
            border-radius: 4px;
        }

        .remember-row label {
            font-size: 13px;
            color: var(--text-secondary);
            cursor: pointer;
            user-select: none;
        }

        /* ═══ Button ═══ */
        .submit-btn {
            width: 100%;
            padding: 12px 16px;
            font-size: 14px;
            font-weight: 600;
            font-family: inherit;
            color: #ffffff;
            background: var(--accent);
            border: none;
            border-radius: 10px;
            cursor: pointer;
            transition: background 0.2s ease, box-shadow 0.2s ease;
            letter-spacing: -0.01em;
        }

        .submit-btn:hover {
            background: var(--accent-hover);
            box-shadow: 0 2px 8px rgba(79, 70, 229, 0.25);
        }

        .submit-btn:active {
            background: var(--accent-hover);
            box-shadow: none;
        }

        /* ═══ Footer ═══ */
        .form-footer {
            text-align: center;
            margin-top: 28px;
            padding-top: 20px;
            border-top: 1px solid var(--border);
        }

        .form-footer p {
            font-size: 12px;
            color: var(--text-muted);
            letter-spacing: 0.02em;
        }

        /* ═══ Error banner (session expired, etc.) ═══ */
        .alert-success {
            background: #f0fdf4;
            border: 1px solid #dcfce7;
            color: #166534;
            padding: 12px 14px;
            border-radius: 10px;
            font-size: 13px;
            margin-bottom: 20px;
            text-align: center;
        }

        .alert-error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
            padding: 12px 14px;
            border-radius: 10px;
            font-size: 13px;
            margin-bottom: 20px;
            text-align: center;
        }

        /* ═══ Responsive ═══ */
        @media (min-width: 1024px) {
            .branding {
                display: flex;
            }
            .mobile-logo {
                display: none;
            }
            .card {
                box-shadow: none;
                border: none;
                padding: 44px 40px;
                background: transparent;
            }
            .form-side {
                background: var(--card);
            }
        }

        @media (max-width: 480px) {
            .card {
                padding: 32px 24px;
            }
            .form-header h1 {
                font-size: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="page">

        {{-- Branding panel (visible on large screens) --}}
        <div class="branding">
            <div class="branding-content">
                <div class="branding-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                </div>
                <h2>YesWeCange Support</h2>
                <p>Gerez vos conversations clients depuis un seul espace simple et efficace.</p>

                <div class="branding-features">
                    <div class="branding-feature">
                        <span class="dot"></span>
                        <span>Conversations en temps reel</span>
                    </div>
                    <div class="branding-feature">
                        <span class="dot"></span>
                        <span>Gestion des contacts et equipes</span>
                    </div>
                    <div class="branding-feature">
                        <span class="dot"></span>
                        <span>Statistiques et performances</span>
                    </div>
                    <div class="branding-feature">
                        <span class="dot"></span>
                        <span>Notifications instantanees</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Form side --}}
        <div class="form-side">
            <div class="form-container">
                <div class="card">

                    {{-- Mobile logo --}}
                    <div class="mobile-logo">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                    </div>

                    <div class="form-header">
                        <h1>Bon retour</h1>
                        <p>Connectez-vous a votre espace agent</p>
                    </div>

                    @if (session('status'))
                        <div class="alert-success">{{ session('status') }}</div>
                    @endif

                    @if ($errors->has('email') && str_contains($errors->first('email'), 'credentials'))
                        <div class="alert-error">Identifiants incorrects. Veuillez reessayer.</div>
                    @endif

                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="field">
                            <label for="email">Email</label>
                            <input
                                type="email"
                                id="email"
                                name="email"
                                placeholder="agent@yeswechange.com"
                                value="{{ old('email') }}"
                                required
                                autofocus
                                autocomplete="username"
                            >
                            @if($errors->has('email') && !str_contains($errors->first('email'), 'credentials'))
                                <span class="error-text show">{{ $errors->first('email') }}</span>
                            @endif
                        </div>

                        <div class="field">
                            <div class="field-header">
                                <label for="password">Mot de passe</label>
                                @if (Route::has('password.request'))
                                    <a href="{{ route('password.request') }}" class="forgot-link">Oublie ?</a>
                                @endif
                            </div>
                            <input
                                type="password"
                                id="password"
                                name="password"
                                placeholder="••••••••"
                                required
                                autocomplete="current-password"
                            >
                            @if($errors->has('password'))
                                <span class="error-text show">{{ $errors->first('password') }}</span>
                            @endif
                        </div>

                        <div class="remember-row">
                            <input type="checkbox" id="remember" name="remember">
                            <label for="remember">Se souvenir de moi</label>
                        </div>

                        <button type="submit" class="submit-btn">
                            Se connecter
                        </button>
                    </form>

                    <div class="form-footer">
                        <p>YesWeCange &middot; Espace Support Agent</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
