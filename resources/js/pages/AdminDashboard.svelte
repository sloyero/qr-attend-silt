<script>

    async function logout() {

        await fetch('/logout', {

            method: 'POST',

            headers: {

                'X-CSRF-TOKEN': document
                    .querySelector('meta[name="csrf-token"]')
                    .content,

            },

        })

        window.location.href = '/login'

    }

</script>

<div class="admin-dash">

    <!-- HEADER -->
    <header class="admin-header animate-slide-up">

        <div>
            <div class="admin-label">
                <span>🛡️</span> Admin Portal
            </div>
            <h1 class="qr-page-title">
                Dashboard Admin
            </h1>
            <p class="qr-page-subtitle">
                Pilih menu pengelolaan sistem
            </p>
        </div>

        <button
            on:click={logout}
            class="qr-btn qr-btn-outline"
        >
            🚪 Logout
        </button>

    </header>

    <!-- MENU CARDS -->
    <div class="admin-grid animate-stagger">

        <!-- DOSEN -->
        <a href="/admin/dosen" class="admin-menu-card">

            <div class="admin-menu-icon admin-menu-icon-blue">
                👨‍🏫
            </div>

            <h2 class="admin-menu-title">
                Kelola Dosen
            </h2>

            <p class="admin-menu-desc">
                Tambah dosen, atur NIDN,
                dan kelola mata kuliah yang diampu.
            </p>

            <div class="admin-menu-cta">
                <span class="qr-btn qr-btn-primary qr-btn-sm">
                    Masuk Menu Dosen →
                </span>
            </div>

        </a>

        <!-- MAHASISWA -->
        <a href="/admin/mahasiswa" class="admin-menu-card">

            <div class="admin-menu-icon admin-menu-icon-green">
                🎓
            </div>

            <h2 class="admin-menu-title">
                Kelola Mahasiswa
            </h2>

            <p class="admin-menu-desc">
                Tambah mahasiswa, hapus mahasiswa,
                dan kelola data akademik mahasiswa.
            </p>

            <div class="admin-menu-cta">
                <span class="qr-btn" style="background: var(--color-success); color: white; padding: 8px 16px; font-size: 13px;">
                    Masuk Menu Mahasiswa →
                </span>
            </div>

        </a>

    </div>

</div>

<style>
    .admin-dash {
        min-height: 100vh;
        background: var(--color-bg);
        padding: 32px 24px;
        max-width: 960px;
        margin: 0 auto;
    }

    .admin-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 48px;
        gap: 16px;
        flex-wrap: wrap;
    }

    .admin-label {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
        font-weight: 700;
        color: var(--color-accent);
        text-transform: uppercase;
        letter-spacing: 0.08em;
        margin-bottom: 8px;
    }

    .admin-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 24px;
    }

    .admin-menu-card {
        display: block;
        background: var(--color-surface);
        border-radius: var(--radius-xl);
        border: 1px solid var(--color-border);
        box-shadow: var(--shadow-sm);
        padding: 40px 32px;
        text-decoration: none;
        color: inherit;
        transition: all var(--transition-base);
    }

    .admin-menu-card:hover {
        box-shadow: var(--shadow-xl);
        transform: translateY(-4px);
        border-color: var(--color-border-hover);
    }

    .admin-menu-icon {
        width: 64px;
        height: 64px;
        border-radius: var(--radius-lg);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 32px;
        margin-bottom: 24px;
    }

    .admin-menu-icon-blue {
        background: var(--color-primary-light);
    }

    .admin-menu-icon-green {
        background: var(--color-success-light);
    }

    .admin-menu-title {
        font-size: 24px;
        font-weight: 800;
        color: var(--color-primary);
        margin: 0 0 8px 0;
        letter-spacing: -0.02em;
    }

    .admin-menu-desc {
        color: var(--color-text-secondary);
        font-size: 15px;
        line-height: 1.6;
        margin: 0;
    }

    .admin-menu-cta {
        margin-top: 24px;
    }

    @media (min-width: 768px) {
        .admin-dash {
            padding: 48px 40px;
        }

        .admin-grid {
            grid-template-columns: 1fr 1fr;
            gap: 32px;
        }
    }

    @media (min-width: 1024px) {
        .admin-dash {
            padding: 64px 48px;
        }
    }
</style>