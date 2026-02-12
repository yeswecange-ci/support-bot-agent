<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - YesWeChange</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --bg-primary: #fafafa;
            --bg-secondary: #ffffff;
            --text-primary: #0a0a0a;
            --text-secondary: #6b6b6b;
            --border: #e5e5e5;
            --accent: #0a0a0a;
            --accent-hover: #2a2a2a;
            --input-focus: #f0f0f0;
        }

        body {
            font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--bg-primary);
            color: var(--text-primary);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            line-height: 1.6;
        }

        .container {
            width: 100%;
            max-width: 440px;
            animation: fadeIn 0.6s ease-out;
        }

        @keyframes fadeIn {
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
            background: var(--bg-secondary);
            border-radius: 16px;
            padding: 48px 40px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
            border: 1px solid var(--border);
        }

        .header {
            text-align: center;
            margin-bottom: 40px;
        }

        .logo {
            display: inline-block;
            width: 48px;
            height: 48px;
            background: var(--accent);
            border-radius: 12px;
            margin-bottom: 24px;
            position: relative;
            overflow: hidden;
        }

        .logo::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 20px;
            height: 20px;
            background: var(--bg-secondary);
            border-radius: 4px;
        }

        h1 {
            font-size: 28px;
            font-weight: 700;
            letter-spacing: -0.02em;
            margin-bottom: 8px;
            color: var(--text-primary);
        }

        .subtitle {
            font-size: 15px;
            color: var(--text-secondary);
            font-weight: 400;
        }

        .form-group {
            margin-bottom: 24px;
        }

        label {
            display: block;
            font-size: 14px;
            font-weight: 500;
            color: var(--text-primary);
            margin-bottom: 8px;
        }

        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 14px 16px;
            font-size: 15px;
            font-family: inherit;
            color: var(--text-primary);
            background: var(--bg-primary);
            border: 1px solid var(--border);
            border-radius: 10px;
            transition: all 0.2s ease;
            outline: none;
        }

        input[type="email"]:focus,
        input[type="password"]:focus {
            background: var(--input-focus);
            border-color: var(--accent);
        }

        input[type="email"]::placeholder,
        input[type="password"]::placeholder {
            color: #a0a0a0;
        }

        .password-wrapper {
            position: relative;
        }

        .forgot-link {
            position: absolute;
            right: 0;
            top: 0;
            font-size: 13px;
            color: var(--text-secondary);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s ease;
        }

        .forgot-link:hover {
            color: var(--text-primary);
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            margin: 24px 0 32px 0;
        }

        input[type="checkbox"] {
            width: 18px;
            height: 18px;
            margin-right: 10px;
            cursor: pointer;
            accent-color: var(--accent);
        }

        .checkbox-label {
            font-size: 14px;
            color: var(--text-secondary);
            cursor: pointer;
            user-select: none;
        }

        .submit-btn {
            width: 100%;
            padding: 16px;
            font-size: 15px;
            font-weight: 600;
            font-family: inherit;
            color: #ffffff;
            background: var(--accent);
            border: none;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.2s ease;
            letter-spacing: -0.01em;
        }

        .submit-btn:hover {
            background: var(--accent-hover);
            transform: translateY(-1px);
        }

        .submit-btn:active {
            transform: translateY(0);
        }

        .footer {
            text-align: center;
            margin-top: 32px;
            padding-top: 24px;
            border-top: 1px solid var(--border);
        }

        .footer-text {
            font-size: 13px;
            color: var(--text-secondary);
        }

        .error-message {
            font-size: 13px;
            color: #dc2626;
            margin-top: 6px;
            display: none;
        }

        .error-message.show {
            display: block;
        }

        .success-message {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            color: #166534;
            padding: 14px 16px;
            border-radius: 10px;
            font-size: 14px;
            margin-bottom: 24px;
            text-align: center;
        }

        @media (max-width: 480px) {
            .card {
                padding: 36px 28px;
            }

            h1 {
                font-size: 24px;
            }

            .subtitle {
                font-size: 14px;
            }
        }

        /* Animation au chargement */
        .form-group {
            animation: slideIn 0.5s ease-out backwards;
        }

        .form-group:nth-child(1) { animation-delay: 0.1s; }
        .form-group:nth-child(2) { animation-delay: 0.2s; }
        .checkbox-group { animation: slideIn 0.5s ease-out 0.3s backwards; }
        .submit-btn { animation: slideIn 0.5s ease-out 0.4s backwards; }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="header">
                <div class="logo"></div>
                <h1>Bienvenue</h1>
                <p class="subtitle">Connectez-vous à votre espace agent</p>
            </div>

            <!-- Message de succès (optionnel) -->
            <!-- <div class="success-message">
                Votre session a expiré. Veuillez vous reconnecter.
            </div> -->

            <form method="POST" action="{{ route('login') }}" id="loginForm">
                @csrf
                
                <div class="form-group">
                    <label for="email">Adresse email</label>
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
                    <span class="error-message" id="emailError">{{ $errors->first('email') }}</span>
                </div>

                <div class="form-group">
                    <div class="password-wrapper">
                        <label for="password">Mot de passe</label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="forgot-link">Mot de passe oublié ?</a>
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
                    <span class="error-message" id="passwordError">{{ $errors->first('password') }}</span>
                </div>

                <div class="checkbox-group">
                    <input 
                        type="checkbox" 
                        id="remember" 
                        name="remember"
                    >
                    <label for="remember" class="checkbox-label">Se souvenir de moi</label>
                </div>

                <button type="submit" class="submit-btn">
                    Se connecter
                </button>
            </form>

            <div class="footer">
                <p class="footer-text">YesWeCange Support</p>
            </div>
        </div>
    </div>

    <script>
        // Animation au focus des inputs
        const inputs = document.querySelectorAll('input[type="email"], input[type="password"]');
        
        inputs.forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.style.transform = 'scale(1.01)';
                this.parentElement.style.transition = 'transform 0.2s ease';
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.style.transform = 'scale(1)';
            });
        });

        // Afficher les erreurs si présentes
        @if($errors->has('email'))
            document.getElementById('emailError').classList.add('show');
        @endif
        
        @if($errors->has('password'))
            document.getElementById('passwordError').classList.add('show');
        @endif
    </script>
</body>
</html>