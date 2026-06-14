<script>

    import { router } from '@inertiajs/svelte'
    import { toast } from '../../stores/toastStore.js'

    let email = ''
    let password = ''
    let loading = false

    function login() {
        if (!email || !password) {
            toast.warning('Email dan password harus diisi!')
            return
        }

        loading = true

        router.post('/login', {
            email,
            password,
        }, {
            onSuccess: () => {
                toast.success('Login berhasil! Mengarahkan...')
            },
            onError: (errors) => {
                toast.error(errors.email || 'Email atau password salah!')
            },
            onFinish: () => {
                loading = false
            }
        })
    }

</script>

<!-- Login page: split layout with branded left panel and form right panel -->
<div class="login-wrapper">

    <!-- LEFT: Brand Panel (hidden on mobile) -->
    <div class="login-brand">
        <div class="login-brand-content animate-fade-in">

            <!-- Floating icon -->
            <div class="login-brand-icon animate-float">
                🎓
            </div>

            <h1 class="login-brand-title">
                QR-Attend
            </h1>

            <p class="login-brand-desc">
                Smart Attendance System untuk pengelolaan
                presensi dosen dan mahasiswa secara modern.
            </p>

            <!-- Decorative dots -->
            <div class="login-brand-dots">
                <span class="dot dot-active"></span>
                <span class="dot"></span>
                <span class="dot"></span>
            </div>

        </div>
    </div>

    <!-- RIGHT: Login Form -->
    <div class="login-form-area">

        <div class="login-card animate-slide-up">

            <!-- Mobile-only logo -->
            <div class="login-mobile-logo">
                <span>🎓</span> QR-Attend
            </div>

            <div class="login-card-header">

                <h1 class="login-card-title">
                    Selamat Datang
                </h1>

                <p class="login-card-desc">
                    Silakan masuk menggunakan akun Anda
                </p>

            </div>

            <form on:submit|preventDefault={login}>

                <!-- EMAIL -->
                <div class="login-field">

                    <label
                        for="email"
                        class="qr-label"
                    >
                        Email
                    </label>

                    <input
                        id="email"
                        bind:value={email}
                        type="email"
                        placeholder="nama@email.com"
                        class="qr-input"
                        autocomplete="email"
                    />

                </div>

                <!-- PASSWORD -->
                <div class="login-field">

                    <label
                        for="password"
                        class="qr-label"
                    >
                        Password
                    </label>

                    <input
                        id="password"
                        bind:value={password}
                        type="password"
                        placeholder="Masukkan password"
                        class="qr-input"
                        autocomplete="current-password"
                    />

                </div>

                <!-- SUBMIT -->
                <button
                    type="submit"
                    disabled={loading}
                    class="qr-btn qr-btn-primary qr-btn-xl login-submit"
                >
                    {#if loading}
                        <svg class="login-spinner" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" stroke-linecap="round" opacity="0.3"/>
                            <path d="M12 2a10 10 0 0 1 10 10" stroke="currentColor" stroke-width="3" stroke-linecap="round"/>
                        </svg>
                        Memproses...
                    {:else}
                        Masuk ke Sistem
                    {/if}
                </button>

            </form>

        </div>

    </div>

</div>

<style>
    /* === Layout === */
    .login-wrapper {
        min-height: 100vh;
        display: flex;
        overflow: hidden;
    }

    /* === Brand Panel === */
    .login-brand {
        display: none;
        width: 45%;
        background: linear-gradient(135deg, #062B66 0%, #0a3d8f 50%, #1e40af 100%);
        color: white;
        align-items: center;
        justify-content: center;
        padding: 64px;
        position: relative;
        overflow: hidden;
    }

    /* Subtle pattern overlay */
    .login-brand::before {
        content: '';
        position: absolute;
        inset: 0;
        background: radial-gradient(circle at 20% 80%, rgba(255,255,255,0.08) 0%, transparent 50%),
                    radial-gradient(circle at 80% 20%, rgba(255,255,255,0.05) 0%, transparent 50%);
    }

    .login-brand-content {
        position: relative;
        z-index: 1;
        text-align: center;
        max-width: 420px;
    }

    .login-brand-icon {
        font-size: 72px;
        margin-bottom: 24px;
    }

    .login-brand-title {
        font-size: 48px;
        font-weight: 900;
        letter-spacing: -0.03em;
        margin: 0 0 16px 0;
    }

    .login-brand-desc {
        font-size: 18px;
        line-height: 1.7;
        color: rgba(255, 255, 255, 0.8);
        margin: 0;
    }

    .login-brand-dots {
        display: flex;
        gap: 8px;
        justify-content: center;
        margin-top: 40px;
    }

    .dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.3);
    }

    .dot-active {
        background: white;
        width: 24px;
        border-radius: 4px;
    }

    /* === Form Area === */
    .login-form-area {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 32px 24px;
        background: var(--color-bg);
    }

    .login-card {
        width: 100%;
        max-width: 480px;
        background: var(--color-surface);
        border-radius: var(--radius-xl);
        border: 1px solid var(--color-border);
        box-shadow: var(--shadow-lg);
        padding: 48px 40px;
    }

    .login-mobile-logo {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 24px;
        font-weight: 800;
        color: var(--color-primary);
        margin-bottom: 32px;
    }

    .login-card-header {
        margin-bottom: 32px;
    }

    .login-card-title {
        font-size: 28px;
        font-weight: 800;
        color: var(--color-primary);
        margin: 0 0 8px 0;
        letter-spacing: -0.02em;
    }

    .login-card-desc {
        font-size: 15px;
        color: var(--color-text-secondary);
        margin: 0;
    }

    .login-field {
        margin-bottom: 20px;
    }

    .login-submit {
        width: 100%;
        margin-top: 8px;
    }

    .login-spinner {
        width: 20px;
        height: 20px;
        animation: spin 0.8s linear infinite;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    /* === Desktop (1024px+) === */
    @media (min-width: 1024px) {
        .login-brand {
            display: flex;
        }

        .login-mobile-logo {
            display: none;
        }

        .login-card {
            padding: 56px 48px;
        }

        .login-card-title {
            font-size: 32px;
        }
    }

    /* === Small mobile (< 480px) === */
    @media (max-width: 480px) {
        .login-card {
            padding: 32px 24px;
            border: none;
            box-shadow: none;
            background: transparent;
        }

        .login-form-area {
            padding: 24px 16px;
        }
    }
</style>