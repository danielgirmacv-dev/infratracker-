<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — InfraTracker</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Inter', system-ui, sans-serif;
            min-height: 100vh;
            min-height: 100dvh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f8fafc;
            background-image:
                radial-gradient(ellipse 100% 80% at 50% -30%, rgba(28, 180, 201, 0.12), transparent),
                linear-gradient(rgba(28, 180, 201, 0.02) 1px, transparent 1px),
                linear-gradient(90deg, rgba(28, 180, 201, 0.02) 1px, transparent 1px);
            background-size: auto, 48px 48px, 48px 48px;
            padding: max(1rem, env(safe-area-inset-top)) max(1rem, env(safe-area-inset-right)) max(1rem, env(safe-area-inset-bottom)) max(1rem, env(safe-area-inset-left));
            overflow-x: hidden;
            overflow-y: auto;
        }
        .orb { position: fixed; border-radius: 50%; filter: blur(80px); opacity: .12; animation: float 8s ease-in-out infinite; pointer-events: none; }
        .orb-1 { width: 300px; height: 300px; background: #1cb4c9; top: -80px; left: -60px; }
        .orb-2 { width: 250px; height: 250px; background: #0b5e6f; bottom: -60px; right: -40px; animation-delay: 3s; }
        @keyframes float { 0%,100% { transform: translateY(0); } 50% { transform: translateY(-30px); } }

        .login-container {
            width: 100%; max-width: 440px;
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(20px) saturate(160%);
            border: 1px solid rgba(28, 180, 201, 0.15);
            border-radius: 1.25rem;
            padding: 2.25rem 2rem;
            box-shadow: 0 0 0 1px rgba(255,255,255,.6), 0 20px 40px rgba(15,23,42,.08);
            animation: slideUp .6s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative; z-index: 1;
        }
        .enterprise-badge {
            display: inline-flex; align-items: center; gap: .35rem; margin-bottom: 1rem;
            padding: .35rem .75rem; border-radius: 999px; font-size: .65rem; font-weight: 700;
            letter-spacing: .08em; text-transform: uppercase; color: #0e7490;
            background: rgba(28, 180, 201, 0.08); border: 1px solid rgba(28, 180, 201, 0.2);
        }
        @keyframes slideUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
        .logo { text-align: center; margin-bottom: 2rem; }
        .form-group { margin-bottom: 1.25rem; }
        .form-label { display: block; font-size: .75rem; font-weight: 600; color: #475569; margin-bottom: .4rem; text-transform: uppercase; letter-spacing: .05em; }
        .form-input {
            width: 100%; padding: .7rem 1rem; border-radius: 12px; font-size: .85rem; color: #0f172a;
            background: rgba(15,23,42,.02); border: 1.5px solid rgba(15,23,42,.08);
            outline: none; transition: all .2s; font-family: inherit;
        }
        .form-input:focus { border-color: rgba(28,180,201,.4); box-shadow: 0 0 0 3px rgba(28,180,201,.1); }
        .form-input::placeholder { color: #94a3b8; }



        .password-wrap { position: relative; }
        .password-wrap .form-input { padding-right: 2.75rem; }
        .password-toggle {
            position: absolute; right: .75rem; top: 50%; transform: translateY(-50%);
            background: none; border: none; padding: .25rem; cursor: pointer; color: #64748b;
        }
        .password-toggle svg { width: 1.15rem; height: 1.15rem; }

        .turnstile-wrap { display: flex; flex-direction: column; align-items: center; margin-bottom: 1.25rem; min-height: 65px; gap: .5rem; }
        .turnstile-hint { display: none; font-size: .7rem; color: #d97706; text-align: center; max-width: 280px; }
        .turnstile-hint.visible { display: block; }

        .btn-login {
            width: 100%; padding: .8rem; border: none; border-radius: 14px; cursor: pointer;
            font-size: .9rem; font-weight: 700; color: #fff;
            background: linear-gradient(135deg, #1cb4c9, #0b5e6f);
            box-shadow: 0 4px 15px rgba(28,180,201,.25);
        }
        .btn-login:hover { transform: translateY(-2px); }

        .error-msg { color: #dc2626; font-size: .75rem; margin-top: .4rem; font-weight: 500; }
        .success-msg {
            background: rgba(16,185,129,.08); border: 1px solid rgba(16,185,129,.2);
            border-radius: 12px; padding: .6rem 1rem; margin-bottom: 1.25rem;
            font-size: .8rem; color: #059669; font-weight: 500;
        }
    </style>
</head>
<body>
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>

    <div class="login-container">
        <div class="text-center">
            <span class="enterprise-badge">Secure enterprise access</span>
        </div>
        <div class="logo">
            <div style="display: flex; align-items: center; justify-content: center; gap: 14px; margin-bottom: 0.5rem;">
                <svg width="40" height="48" viewBox="0 0 40 48" fill="none"><rect x="14" y="0" width="12" height="48" fill="#1cb4c9"/><rect x="20" y="0" width="20" height="8" rx="2.5" fill="#1cb4c9"/><rect x="0" y="8" width="20" height="8" rx="2.5" fill="#1cb4c9"/><rect x="20" y="16" width="20" height="8" rx="2.5" fill="#1cb4c9"/><rect x="0" y="24" width="20" height="8" rx="2.5" fill="#1cb4c9"/><rect x="20" y="32" width="20" height="8" rx="2.5" fill="#1cb4c9"/><rect x="0" y="40" width="20" height="8" rx="2.5" fill="#1cb4c9"/></svg>
                <h1 style="font-size: 3.5rem; font-weight: 800; color: #0f172a; margin: 0; line-height: 1;">EEC</h1>
            </div>
            <p style="font-size: .85rem; color: #0891b2;">Infrastructure Project Management</p>
        </div>

        @if(session('success'))
            <div class="success-msg">{{ session('success') }}</div>
        @endif

        <form method="POST" action="{{ url('/login') }}" id="login-form">
            @csrf
            <div class="form-group">
                <label for="email" class="form-label">Username or Email Address</label>
                <input
                    type="text"
                    id="email"
                    name="email"
                    class="form-input"
                    value="{{ old('email') }}"
                    placeholder="Enter your username or email address"
                    required
                    autocomplete="username"
                    autofocus
                >
                @error('email') <p class="error-msg">{{ $message }}</p> @enderror
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <div class="password-wrap">
                    <input type="password" id="password" name="password" class="form-input" placeholder="Enter your password" required autocomplete="current-password">
                    <button type="button" class="password-toggle" id="password-toggle" aria-label="Show password">
                        <svg id="icon-eye" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>
                        <svg id="icon-eye-slash" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="display:none"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" /></svg>
                    </button>
                </div>
                @error('password') <p class="error-msg">{{ $message }}</p> @enderror
            </div>

            @if($turnstileSiteKey)
                <div class="form-group">
                    <div class="turnstile-wrap">
                        <div id="cf-turnstile-container"></div>
                        <p id="cf-turnstile-hint" class="turnstile-hint">Turnstile could not load.</p>
                    </div>
                    @error('turnstile') <p class="error-msg text-center">{{ $message }}</p> @enderror
                </div>
            @endif

            <button type="submit" class="btn-login">Sign In</button>
        </form>
    </div>

    @if($turnstileSiteKey)
        <script src="https://challenges.cloudflare.com/turnstile/v0/api.js?render=explicit" defer></script>
        <script>
            (function () {
                var siteKey = @json($turnstileSiteKey);
                function renderTurnstile() {
                    if (typeof turnstile === 'undefined') return;
                    try {
                        turnstile.render('#cf-turnstile-container', { sitekey: siteKey, theme: 'light', size: 'normal' });
                    } catch (e) {}
                }
                if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', renderTurnstile);
                else renderTurnstile();
            })();
        </script>
    @endif

    <script>
        (function () {
            var password = document.getElementById('password');

            var toggle = document.getElementById('password-toggle');
            var iconEye = document.getElementById('icon-eye');
            var iconEyeSlash = document.getElementById('icon-eye-slash');
            if (toggle && password) {
                toggle.addEventListener('click', function () {
                    var show = password.type === 'password';
                    password.type = show ? 'text' : 'password';
                    iconEye.style.display = show ? 'none' : '';
                    iconEyeSlash.style.display = show ? '' : 'none';
                });
            }
        })();
    </script>
</body>
</html>
