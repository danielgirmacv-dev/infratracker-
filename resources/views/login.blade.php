<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — InfraTracker</title>
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
            background: #061018;
            background-image:
                radial-gradient(ellipse 100% 80% at 50% -30%, rgba(28, 180, 201, 0.18), transparent),
                linear-gradient(rgba(28, 180, 201, 0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(28, 180, 201, 0.03) 1px, transparent 1px);
            background-size: auto, 48px 48px, 48px 48px;
            padding: max(1rem, env(safe-area-inset-top)) max(1rem, env(safe-area-inset-right)) max(1rem, env(safe-area-inset-bottom)) max(1rem, env(safe-area-inset-left));
            overflow-x: hidden;
            overflow-y: auto;
        }
        /* Floating orbs */
        .orb { position: fixed; border-radius: 50%; filter: blur(80px); opacity: .15; animation: float 8s ease-in-out infinite; pointer-events: none; }
        .orb-1 { width: 300px; height: 300px; background: #1cb4c9; top: -80px; left: -60px; }
        .orb-2 { width: 250px; height: 250px; background: #0b5e6f; bottom: -60px; right: -40px; animation-delay: 3s; }
        @keyframes float { 0%,100% { transform: translateY(0); } 50% { transform: translateY(-30px); } }

        .login-container {
            width: 100%; max-width: 440px;
            background: rgba(12, 30, 46, 0.75);
            backdrop-filter: blur(20px) saturate(160%);
            -webkit-backdrop-filter: blur(20px) saturate(160%);
            border: 1px solid rgba(28, 180, 201, 0.2);
            border-radius: 1.25rem;
            padding: 2.25rem 2rem;
            box-shadow: 0 0 0 1px rgba(255,255,255,.04), 0 24px 64px rgba(0,0,0,.45), 0 0 80px rgba(28,180,201,.08);
            animation: slideUp .6s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative; z-index: 1;
        }
        .login-container::before {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: inherit;
            padding: 1px;
            background: linear-gradient(135deg, rgba(28,180,201,.35), transparent 50%);
            -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            -webkit-mask-composite: xor;
            mask-composite: exclude;
            pointer-events: none;
        }
        .enterprise-badge {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            margin-bottom: 1rem;
            padding: .35rem .75rem;
            border-radius: 999px;
            font-size: .65rem;
            font-weight: 700;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: #67e8f9;
            background: rgba(28, 180, 201, 0.12);
            border: 1px solid rgba(28, 180, 201, 0.25);
        }
        @keyframes slideUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }

        .logo { text-align: center; margin-bottom: 2rem; }
        .logo-img { max-width: 200px; height: auto; margin-bottom: 1rem; }
        .logo h1 { font-size: 1.5rem; font-weight: 800; color: #f1f5f9; letter-spacing: -.02em; }
        .logo p { font-size: .8rem; color: #a5f3fc; margin-top: .25rem; }

        .role-grid { display: flex; flex-direction: column; gap: .6rem; margin-bottom: 1.5rem; }
        .role-option { display: none; }
        .role-label {
            display: flex; align-items: center; gap: .85rem;
            padding: .85rem 1rem; border-radius: 14px; cursor: pointer;
            border: 1.5px solid rgba(255,255,255,.06);
            background: rgba(255,255,255,.03);
            transition: all .25s ease;
        }
        .role-label:hover { background: rgba(28,180,201,.08); border-color: rgba(28,180,201,.3); transform: translateX(4px); }
        .role-option:checked + .role-label { background: rgba(28,180,201,.12); border-color: rgba(28,180,201,.5); box-shadow: 0 0 20px rgba(28,180,201,.15); }
        .role-avatar {
            width: 40px; height: 40px; border-radius: 12px; display: flex; align-items: center; justify-content: center;
            font-weight: 800; font-size: .85rem; flex-shrink: 0;
        }
        .role-avatar.director { background: rgba(28,180,201,.2); color: #67e8f9; }
        .role-avatar.manager { background: rgba(11,94,111,.3); color: #22d3ee; }
        .role-avatar.employee { background: rgba(255,255,255,.1); color: #fff; }
        .role-info { flex: 1; }
        .role-name { font-size: .85rem; font-weight: 700; color: #e2e8f0; }
        .role-desc { font-size: .7rem; color: #64748b; margin-top: .1rem; }
        .role-check { width: 20px; height: 20px; border-radius: 50%; border: 2px solid rgba(255,255,255,.12); display: flex; align-items: center; justify-content: center; transition: all .2s; }
        .role-option:checked + .role-label .role-check { border-color: #1cb4c9; background: #1cb4c9; }
        .role-option:checked + .role-label .role-check svg { opacity: 1; }
        .role-check svg { width: 12px; height: 12px; color: #fff; opacity: 0; transition: opacity .2s; }

        .form-group { margin-bottom: 1.25rem; }
        .form-label { display: block; font-size: .75rem; font-weight: 600; color: #94a3b8; margin-bottom: .4rem; text-transform: uppercase; letter-spacing: .05em; }
        .form-input {
            width: 100%; padding: .7rem 1rem; border-radius: 12px; font-size: .85rem; color: #e2e8f0;
            background: rgba(255,255,255,.05); border: 1.5px solid rgba(255,255,255,.08);
            outline: none; transition: all .2s; font-family: inherit;
        }
        .form-input:focus { border-color: rgba(28,180,201,.5); box-shadow: 0 0 0 3px rgba(28,180,201,.15); }
        .form-input::placeholder { color: #475569; }

        .password-wrap { position: relative; }
        .password-wrap .form-input { padding-right: 2.75rem; }
        .password-toggle {
            position: absolute; right: .75rem; top: 50%; transform: translateY(-50%);
            background: none; border: none; padding: .25rem; cursor: pointer;
            color: #64748b; display: flex; align-items: center; justify-content: center;
            transition: color .2s;
        }
        .password-toggle:hover { color: #94a3b8; }
        .password-toggle:focus { outline: none; color: #1cb4c9; }
        .password-toggle svg { width: 1.15rem; height: 1.15rem; }

        .turnstile-wrap {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.25rem;
            min-height: 65px;
            gap: .5rem;
        }
        .turnstile-hint {
            display: none;
            font-size: .7rem;
            color: #fbbf24;
            text-align: center;
            line-height: 1.5;
            max-width: 280px;
        }
        .turnstile-hint.visible { display: block; }

        .btn-login {
            width: 100%; padding: .8rem; border: none; border-radius: 14px; cursor: pointer;
            font-size: .9rem; font-weight: 700; font-family: inherit; color: #fff;
            background: linear-gradient(135deg, #1cb4c9, #0b5e6f);
            box-shadow: 0 4px 15px rgba(28,180,201,.4);
            transition: all .3s ease;
        }
        .btn-login:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(28,180,201,.5); }
        .btn-login:active { transform: translateY(0); }

        .error-msg { color: #f87171; font-size: .75rem; margin-top: .4rem; font-weight: 500; }
        .success-msg {
            background: rgba(16,185,129,.1); border: 1px solid rgba(16,185,129,.3);
            border-radius: 12px; padding: .6rem 1rem; margin-bottom: 1.25rem;
            font-size: .8rem; color: #34d399; font-weight: 500;
        }

        @media (max-width: 480px) {
            .login-container {
                padding: 1.5rem 1.25rem;
                border-radius: 1rem;
            }
            .logo h1, .logo h1[style] {
                font-size: 2.25rem !important;
            }
            .role-label:hover {
                transform: none;
            }
            .role-name { font-size: .8rem; }
            .role-desc { font-size: .65rem; }
            .role-avatar {
                width: 36px;
                height: 36px;
                font-size: .75rem;
            }
        }
    </style>
</head>
<body>
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>

    <div class="login-container">
        <div class="text-center">
            <span class="enterprise-badge">
                <svg width="12" height="12" fill="currentColor" viewBox="0 0 24 24"><path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4z"/></svg>
                Secure enterprise access
            </span>
        </div>
        <div class="logo">
            <div style="display: flex; align-items: center; justify-content: center; gap: 14px; margin-bottom: 0.5rem;">
                <svg width="40" height="48" viewBox="0 0 40 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <!-- EEC Logo SVG recreation -->
                    <rect x="14" y="0" width="12" height="48" fill="#1cb4c9"/>
                    <rect x="20" y="0" width="20" height="8" rx="2.5" fill="#1cb4c9"/>
                    <rect x="0" y="8" width="20" height="8" rx="2.5" fill="#1cb4c9"/>
                    <rect x="20" y="16" width="20" height="8" rx="2.5" fill="#1cb4c9"/>
                    <rect x="0" y="24" width="20" height="8" rx="2.5" fill="#1cb4c9"/>
                    <rect x="20" y="32" width="20" height="8" rx="2.5" fill="#1cb4c9"/>
                    <rect x="0" y="40" width="20" height="8" rx="2.5" fill="#1cb4c9"/>
                </svg>
                <h1 style="font-size: 3.5rem; font-weight: 800; color: #ffffff; margin: 0; line-height: 1; letter-spacing: 1px;">EEC</h1>
            </div>
            <p style="font-size: .85rem; color: #a5f3fc;">Infrastructure Project Management</p>
        </div>

        @if(session('success'))
            <div class="success-msg">{{ session('success') }}</div>
        @endif

        <form method="POST" action="{{ url('/login') }}">
            @csrf
            <div class="form-group">
                <label class="form-label">Select Your Role</label>
                <div class="role-grid">
                    <input type="radio" name="actor" value="Infra Director" id="role-director" class="role-option" {{ old('actor') === 'Infra Director' ? 'checked' : '' }}>
                    <label for="role-director" class="role-label">
                        <div class="role-avatar director">ID</div>
                        <div class="role-info"><div class="role-name">Infra Director</div><div class="role-desc">Full oversight & task delegation</div></div>
                        <div class="role-check"><svg fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg></div>
                    </label>

                    <input type="radio" name="actor" value="Project Manager" id="role-manager" class="role-option" {{ old('actor') === 'Project Manager' ? 'checked' : '' }}>
                    <label for="role-manager" class="role-label">
                        <div class="role-avatar manager">PC</div>
                        <div class="role-info"><div class="role-name">Project Cordinator</div><div class="role-desc">Coordinate projects & teams</div></div>
                        <div class="role-check"><svg fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg></div>
                    </label>

                    @forelse($employees as $employee)
                        <input type="radio" name="actor" value="{{ $employee->name }}" id="role-{{ $employee->id }}" class="role-option" {{ old('actor', '') === $employee->name || old('actor') === 'Employee' && $employee->name === 'FEVEN' ? 'checked' : '' }}>
                        <label for="role-{{ $employee->id }}" class="role-label">
                            <div class="role-avatar employee">{{ strtoupper(substr($employee->name, 0, 2)) }}</div>
                            <div class="role-info"><div class="role-name">{{ $employee->name }}</div><div class="role-desc">Execute tasks & report progress</div></div>
                            <div class="role-check"><svg fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg></div>
                        </label>
                    @empty
                        <p class="text-center text-xs text-slate-500 py-2">No employees registered. Director must run migration and add employees.</p>
                    @endforelse
                </div>
                @error('actor') <p class="error-msg">{{ $message }}</p> @enderror
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <div class="password-wrap">
                    <input type="password" id="password" name="password" class="form-input" placeholder="Enter your password" required autocomplete="current-password">
                    <button type="button" class="password-toggle" id="password-toggle" aria-label="Show password" aria-pressed="false">
                        <svg id="icon-eye" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        </svg>
                        <svg id="icon-eye-slash" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" style="display: none;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                        </svg>
                    </button>
                </div>
                @error('password') <p class="error-msg">{{ $message }}</p> @enderror
            </div>

            @if($turnstileSiteKey)
                <div class="form-group">
                    <div class="turnstile-wrap">
                        <div id="cf-turnstile-container"></div>
                        <p id="cf-turnstile-hint" class="turnstile-hint">
                            Turnstile could not load. Add <strong>localhost</strong> and <strong>127.0.0.1</strong> in Cloudflare Turnstile hostnames, or set <code>TURNSTILE_USE_TEST_KEYS=true</code> in .env for local dev.
                        </p>
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
                var hint = document.getElementById('cf-turnstile-hint');

                function showHint() {
                    if (hint) hint.classList.add('visible');
                }

                function renderTurnstile() {
                    if (typeof turnstile === 'undefined') {
                        showHint();
                        return;
                    }
                    try {
                        turnstile.render('#cf-turnstile-container', {
                            sitekey: siteKey,
                            theme: 'dark',
                            size: 'normal',
                            'error-callback': showHint,
                            'timeout-callback': showHint,
                        });
                    } catch (e) {
                        showHint();
                    }
                }

                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', renderTurnstile);
                } else {
                    renderTurnstile();
                }
            })();
        </script>
    @endif

    <script>
        (function () {
            var input = document.getElementById('password');
            var toggle = document.getElementById('password-toggle');
            var iconEye = document.getElementById('icon-eye');
            var iconEyeSlash = document.getElementById('icon-eye-slash');
            if (!input || !toggle) return;

            toggle.addEventListener('click', function () {
                var show = input.type === 'password';
                input.type = show ? 'text' : 'password';
                toggle.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
                toggle.setAttribute('aria-pressed', show ? 'true' : 'false');
                iconEye.style.display = show ? 'none' : '';
                iconEyeSlash.style.display = show ? '' : 'none';
            });
        })();
    </script>
</body>
</html>
