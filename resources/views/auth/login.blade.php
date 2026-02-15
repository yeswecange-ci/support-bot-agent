<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - YesWeCange</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        :root {
            --bg-gradient-start: #f8fafc;
            --bg-gradient-end: #e0e7ff;
            --card: #ffffff;
            --text: #0f172a;
            --text-secondary: #475569;
            --text-muted: #94a3b8;
            --border: #e2e8f0;
            --accent: #4f46e5;
            --accent-light: #eef2ff;
            --accent-hover: #4338ca;
            --accent-ring: rgba(79, 70, 229, 0.12);
            --input-bg: #ffffff;
            --error: #ef4444;
            --success: #10b981;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        html {
            height: 100%;
            overflow: hidden;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(135deg, var(--bg-gradient-start) 0%, var(--bg-gradient-end) 100%);
            color: var(--text);
            height: 100%;
            min-height: 100vh;
            display: flex;
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            position: relative;
            overflow: hidden;
        }

        /* ═══ Animated background elements ═══ */
        body::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 800px;
            height: 800px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(79, 70, 229, 0.08) 0%, transparent 70%);
            animation: float 20s ease-in-out infinite;
        }

        body::after {
            content: '';
            position: absolute;
            bottom: -40%;
            left: -10%;
            width: 600px;
            height: 600px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(99, 102, 241, 0.06) 0%, transparent 70%);
            animation: float 15s ease-in-out infinite reverse;
        }

        @keyframes float {
            0%, 100% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(30px, -30px) scale(1.05); }
        }

        /* ═══ Layout split ═══ */
        .page {
            display: flex;
            width: 100%;
            height: 100vh;
            position: relative;
            z-index: 1;
            overflow: hidden;
        }

        .branding {
            display: none;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            width: 55%;
            max-width: 700px;
            padding: 60px 50px;
            background: linear-gradient(135deg, #1e293b 0%, #334155 50%, #475569 100%);
            position: relative;
            overflow: hidden;
        }

        /* Animated mesh gradient effect with better visibility */
        .branding::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: 
                radial-gradient(circle at 20% 50%, rgba(79, 70, 229, 0.15) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(99, 102, 241, 0.12) 0%, transparent 50%),
                radial-gradient(circle at 40% 20%, rgba(139, 92, 246, 0.1) 0%, transparent 50%);
            animation: meshMove 15s ease-in-out infinite;
        }

        @keyframes meshMove {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.8; }
        }

        .branding-content {
            position: relative;
            z-index: 2;
            text-align: center;
            max-width: 520px;
            width: 100%;
        }

        /* Logo container with better styling */
        .branding-logo {
            width: 100px;
            height: 100px;
            margin: 0 auto 32px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 2px solid rgba(255, 255, 255, 0.2);
            border-radius: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s ease;
        }

        .branding-logo:hover {
            transform: scale(1.05);
        }

        /* If you have a logo image */
        .branding-logo img {
            width: 60px;
            height: 60px;
            object-fit: contain;
        }

        /* If using icon instead */
        .branding-logo svg {
            width: 48px;
            height: 48px;
            color: white;
            filter: drop-shadow(0 2px 8px rgba(0, 0, 0, 0.3));
        }

        .branding h2 {
            font-size: 28px;
            font-weight: 800;
            color: #ffffff;
            letter-spacing: -0.03em;
            margin-bottom: 14px;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        }

        .branding > p {
            font-size: 16px;
            color: rgba(255, 255, 255, 0.95);
            line-height: 1.6;
            font-weight: 400;
            margin-bottom: 48px;
            padding: 0 20px;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        }

        .branding-features {
            text-align: left;
            display: flex;
            flex-direction: column;
            gap: 16px;
            width: 100%;
        }

        .branding-feature {
            display: flex;
            align-items: flex-start;
            gap: 14px;
            font-size: 14px;
            color: #ffffff;
            padding: 18px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 14px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .branding-feature:hover {
            background: rgba(255, 255, 255, 0.15);
            transform: translateX(4px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .branding-feature-icon {
            width: 36px;
            height: 36px;
            min-width: 36px;
            border-radius: 9px;
            background: rgba(79, 70, 229, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 2px 8px rgba(79, 70, 229, 0.2);
        }

        .branding-feature-icon svg {
            width: 18px;
            height: 18px;
            color: white;
        }

        .branding-feature-content h4 {
            font-weight: 700;
            margin-bottom: 3px;
            font-size: 15px;
            line-height: 1.3;
            color: #ffffff;
        }

        .branding-feature-content p {
            font-size: 13px;
            color: rgba(255, 255, 255, 0.85);
            line-height: 1.5;
        }

        /* ═══ Form side ═══ */
        .form-side {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 24px;
            position: relative;
            overflow-y: auto;
        }

        .form-container {
            width: 100%;
            max-width: 440px;
            animation: slideUp 0.6s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .card {
            background: var(--card);
            border-radius: 24px;
            padding: 48px 40px;
            box-shadow: var(--shadow-xl);
            border: 1px solid rgba(255, 255, 255, 0.6);
            position: relative;
            overflow: hidden;
        }

        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #4f46e5 0%, #6366f1 100%);
        }

        /* ═══ Header ═══ */
        .form-header {
            margin-bottom: 36px;
            text-align: center;
        }

        /* Mobile logo in card */
        .mobile-logo {
            width: 72px;
            height: 72px;
            background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 28px;
            box-shadow: 0 4px 14px rgba(79, 70, 229, 0.25);
            position: relative;
        }

        .mobile-logo::after {
            content: '';
            position: absolute;
            inset: -2px;
            border-radius: 22px;
            padding: 2px;
            background: linear-gradient(135deg, rgba(79, 70, 229, 0.4), rgba(124, 58, 237, 0.4));
            -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            -webkit-mask-composite: xor;
            mask-composite: exclude;
        }

        /* If you have a logo image for mobile */
        .mobile-logo img {
            width: 40px;
            height: 40px;
            object-fit: contain;
        }

        .mobile-logo svg {
            width: 32px;
            height: 32px;
            color: white;
        }

        .form-header h1 {
            font-size: 28px;
            font-weight: 800;
            letter-spacing: -0.03em;
            color: var(--text);
            margin-bottom: 8px;
            background: linear-gradient(135deg, var(--text) 0%, var(--text-secondary) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .form-header p {
            font-size: 15px;
            color: var(--text-secondary);
            font-weight: 400;
        }

        /* ═══ Form ═══ */
        .field {
            margin-bottom: 24px;
        }

        .field label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 8px;
            letter-spacing: -0.01em;
        }

        .field-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 8px;
        }

        .field input[type="email"],
        .field input[type="password"] {
            width: 100%;
            padding: 14px 16px;
            font-size: 15px;
            font-family: inherit;
            color: var(--text);
            background: var(--input-bg);
            border: 2px solid var(--border);
            border-radius: 12px;
            outline: none;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .field input:hover {
            border-color: #cbd5e1;
        }

        .field input:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 4px var(--accent-ring);
            background: #ffffff;
        }

        .field input::placeholder {
            color: var(--text-muted);
        }

        .forgot-link {
            font-size: 13px;
            color: var(--accent);
            text-decoration: none;
            font-weight: 600;
            transition: all 0.2s;
        }

        .forgot-link:hover {
            color: var(--accent-hover);
            text-decoration: underline;
        }

        .error-text {
            font-size: 13px;
            color: var(--error);
            margin-top: 8px;
            display: none;
            font-weight: 500;
        }

        .error-text.show {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .error-text::before {
            content: '⚠';
            font-size: 14px;
        }

        /* ═══ Checkbox ═══ */
        .remember-row {
            display: flex;
            align-items: center;
            margin: 28px 0 32px;
        }

        .remember-row input[type="checkbox"] {
            width: 18px;
            height: 18px;
            margin-right: 10px;
            accent-color: var(--accent);
            cursor: pointer;
            border-radius: 5px;
        }

        .remember-row label {
            font-size: 14px;
            color: var(--text-secondary);
            cursor: pointer;
            user-select: none;
            font-weight: 500;
        }

        /* ═══ Button ═══ */
        .submit-btn {
            width: 100%;
            padding: 14px 20px;
            font-size: 15px;
            font-weight: 700;
            font-family: inherit;
            color: #ffffff;
            background: linear-gradient(135deg, var(--accent) 0%, #6366f1 100%);
            border: none;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            letter-spacing: -0.01em;
            box-shadow: 0 4px 14px rgba(79, 70, 229, 0.3);
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .submit-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .submit-btn:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(79, 70, 229, 0.4);
        }

        .submit-btn:hover:not(:disabled)::before {
            left: 100%;
        }

        .submit-btn:active:not(:disabled) {
            transform: translateY(0);
            box-shadow: 0 2px 8px rgba(79, 70, 229, 0.3);
        }

        .submit-btn:disabled {
            cursor: not-allowed;
            opacity: 0.7;
        }

        /* ═══ Loader ═══ */
        .btn-loader {
            width: 18px;
            height: 18px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 0.6s linear infinite;
            display: none;
        }

        .submit-btn.loading .btn-loader {
            display: block;
        }

        .submit-btn.loading .btn-text {
            display: none;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* ═══ Footer ═══ */
        .form-footer {
            text-align: center;
            margin-top: 32px;
            padding-top: 24px;
            border-top: 2px solid var(--border);
        }

        .form-footer p {
            font-size: 13px;
            color: var(--text-muted);
            font-weight: 500;
            letter-spacing: 0.01em;
        }

        /* ═══ Alert banners ═══ */
        .alert-success {
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
            border: 2px solid #86efac;
            color: #166534;
            padding: 14px 16px;
            border-radius: 12px;
            font-size: 14px;
            margin-bottom: 24px;
            text-align: center;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            box-shadow: 0 2px 8px rgba(34, 197, 94, 0.1);
        }

        .alert-success::before {
            content: '✓';
            width: 20px;
            height: 20px;
            background: var(--success);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: bold;
        }

        .alert-error {
            background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
            border: 2px solid #fca5a5;
            color: #991b1b;
            padding: 14px 16px;
            border-radius: 12px;
            font-size: 14px;
            margin-bottom: 24px;
            text-align: center;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            box-shadow: 0 2px 8px rgba(239, 68, 68, 0.1);
        }

        .alert-error::before {
            content: '✕';
            width: 20px;
            height: 20px;
            background: var(--error);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: bold;
        }

        /* ═══ Responsive ═══ */
        @media (min-width: 1024px) {
            .branding {
                display: flex;
            }
            .mobile-logo {
                display: none;
            }
            .form-header {
                text-align: left;
            }
        }

        @media (min-width: 1440px) {
            .branding {
                width: 50%;
            }
        }

        @media (max-width: 1023px) {
            .card {
                border-radius: 20px;
                padding: 40px 32px;
            }
        }

        @media (max-width: 480px) {
            .form-side {
                padding: 24px 16px;
            }
            .card {
                padding: 32px 24px;
                border-radius: 16px;
            }
            .form-header h1 {
                font-size: 24px;
            }
            .mobile-logo {
                width: 64px;
                height: 64px;
            }
        }

        /* ═══ Focus visible for accessibility ═══ */
        *:focus-visible {
            outline: 2px solid var(--accent);
            outline-offset: 2px;
        }
    </style>
</head>
<body>
    <div class="page">

        {{-- Branding panel (visible on large screens) --}}
        <div class="branding">
            <div class="branding-content">
                <div class="branding-logo">
                    {{-- Option 1: Si vous avez un logo image --}}
                    <img src="{{ asset('images/logoywc.png') }}" alt="YesWeCange Logo">
                    
                    {{-- Option 2: Icône SVG par défaut 
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>--}}
                </div>
                <!-- <h2>YesWeCange Support</h2>
                <p>Plateforme moderne de gestion des conversations clients. Centralisez vos échanges et offrez une expérience exceptionnelle.</p> -->

                <div class="branding-features">
                    <div class="branding-feature">
                        <div class="branding-feature-icon">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                        </div>
                        <div class="branding-feature-content">
                            <h4>Temps réel</h4>
                            <p>Répondez instantanément à vos clients avec notre système de messagerie</p>
                        </div>
                    </div>
                    <div class="branding-feature">
                        <div class="branding-feature-icon">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <div class="branding-feature-content">
                            <h4>Collaboration d'équipe</h4>
                            <p>Gérez vos équipes et assignez les conversations efficacement</p>
                        </div>
                    </div>
                    <div class="branding-feature">
                        <div class="branding-feature-icon">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                        </div>
                        <div class="branding-feature-content">
                            <h4>Analyses & Rapports</h4>
                            <p>Suivez vos performances avec des statistiques détaillées</p>
                        </div>
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
                        {{-- Option 1: Si vous avez un logo image --}}
                        {{-- <img src="{{ asset('images/logo.png') }}" alt="YesWeCange Logo"> --}}
                        
                        {{-- Option 2: Icône SVG par défaut --}}
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                    </div>

                    <div class="form-header">
                        <h1>Bon retour !</h1>
                        <p>Connectez-vous pour accéder à votre espace</p>
                    </div>

                    @if (session('status'))
                        <div class="alert-success">{{ session('status') }}</div>
                    @endif

                    @if ($errors->has('email') && str_contains($errors->first('email'), 'credentials'))
                        <div class="alert-error">Identifiants incorrects. Veuillez réessayer.</div>
                    @endif

                    <form method="POST" action="{{ route('login') }}" id="loginForm">
                        @csrf

                        <div class="field">
                            <label for="email">Adresse email</label>
                            <input
                                type="email"
                                id="email"
                                name="email"
                                placeholder="votre.email@exemple.com"
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
                                    <a href="{{ route('password.request') }}" class="forgot-link">Mot de passe oublié ?</a>
                                @endif
                            </div>
                            <input
                                type="password"
                                id="password"
                                name="password"
                                placeholder="Entrez votre mot de passe"
                                required
                                autocomplete="current-password"
                            >
                            @if($errors->has('password'))
                                <span class="error-text show">{{ $errors->first('password') }}</span>
                            @endif
                        </div>

                        <div class="remember-row">
                            <input type="checkbox" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}>
                            <label for="remember">Se souvenir de moi</label>
                        </div>

                        <button type="submit" class="submit-btn" id="submitBtn">
                            <div class="btn-loader"></div>
                            <span class="btn-text">Se connecter</span>
                        </button>
                    </form>

                    <div class="form-footer">
                        <p>© 2026 YesWeCange · Tous droits réservés</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Gérer le loader lors de la soumission du formulaire
        const loginForm = document.getElementById('loginForm');
        const submitBtn = document.getElementById('submitBtn');

        loginForm.addEventListener('submit', function(e) {
            // Ajouter la classe loading au bouton
            submitBtn.classList.add('loading');
            submitBtn.disabled = true;
        });

        // Si le formulaire revient avec des erreurs, retirer le loader
        window.addEventListener('pageshow', function(event) {
            if (event.persisted || (window.performance && window.performance.navigation.type === 2)) {
                submitBtn.classList.remove('loading');
                submitBtn.disabled = false;
            }
        });
    </script>
</body>
</html>