<script>

    import { onMount } from 'svelte'

    export let user
    export let kehadirans = []

    let totalHadir = 0
    let totalTelat = 0
    let totalIzin = 0
    let totalAlpha = 0
    let sidebarOpen = false

    async function logout() {
        await fetch('/logout', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
            },
        })
        window.location.href = '/login'
    }

    function toggleSidebar() { sidebarOpen = !sidebarOpen }
    function closeSidebar() { sidebarOpen = false }

    onMount(() => {
        if (Array.isArray(kehadirans)) {
            totalHadir = kehadirans.filter(k => k.status === 'hadir').length
            totalTelat = kehadirans.filter(k => k.status === 'telat').length
            totalIzin = kehadirans.filter(k => k.status === 'izin').length
            totalAlpha = kehadirans.filter(k => k.status === 'alpha').length
        }
    })

</script>

<div class="layout">

    <!-- HAMBURGER -->
    <button class="qr-hamburger" on:click={toggleSidebar} aria-label="Toggle menu">
        {#if sidebarOpen}✕{:else}☰{/if}
    </button>

    <!-- svelte-ignore a11y_click_events_have_key_events -->
    <!-- svelte-ignore a11y_no_static_element_interactions -->
    <div class="qr-sidebar-overlay" class:open={sidebarOpen} on:click={closeSidebar}></div>

    <!-- SIDEBAR -->
    <aside class="qr-sidebar" class:open={sidebarOpen}>
        <div style="margin-bottom: 40px;">
            <h1 style="font-size: 24px; font-weight: 800; color: var(--color-primary); margin: 0;">QR-Attend</h1>
        </div>

        <nav style="display: flex; flex-direction: column; gap: 4px; flex: 1;">
            <a href="/mahasiswa/dashboard" class="qr-nav-link active" on:click={closeSidebar}>📊 Dashboard</a>
            <a href="/presensi" class="qr-nav-link" on:click={closeSidebar}>🎯 Scan Presensi</a>
            <a href="/riwayat-absensi" class="qr-nav-link" on:click={closeSidebar}>📄 Riwayat Absensi</a>
            <a href="/akun" class="qr-nav-link" on:click={closeSidebar}>⚙️ Profile</a>
        </nav>

        <button on:click={logout} class="qr-btn qr-btn-outline" style="width: 100%; margin-top: 16px; color: var(--color-danger);">
            🚪 Logout
        </button>
    </aside>

    <!-- CONTENT -->
    <main class="qr-main" style="flex: 1; padding: 32px 24px; overflow-y: auto;">

        <!-- TOPBAR -->
        <div class="topbar animate-fade-in">
            <div>
                <h1 class="qr-page-title">Dashboard Mahasiswa</h1>
                <p class="qr-page-subtitle">Pantau kehadiran Anda dan scan QR Code kelas.</p>
            </div>
            <div class="topbar-user">
                <div style="text-align: right;">
                    <h2 style="font-weight: 700; color: var(--color-text); font-size: 15px; margin: 0;">{user.name}</h2>
                    <p style="color: var(--color-text-muted); font-size: 13px; margin: 2px 0 0 0;">{user.nim}</p>
                </div>
            </div>
        </div>

        <!-- PROFILE CARD -->
        <div class="qr-card animate-slide-up" style="padding: 32px; margin-bottom: 32px;">
            <div class="profile-row">
                <div class="profile-avatar">
                    {user.name.charAt(0).toUpperCase()}
                </div>
                <div>
                    <h2 style="font-size: 22px; font-weight: 800; color: var(--color-primary); margin: 0 0 8px 0;">{user.name}</h2>
                    <p style="color: var(--color-text-secondary); font-size: 14px; margin: 0 0 4px 0;">
                        <span style="font-weight: 600;">NIM:</span> {user.nim || '-'}
                    </p>
                    <p style="color: var(--color-text-secondary); font-size: 14px; margin: 0;">
                        <span style="font-weight: 600;">Email:</span> {user.email}
                    </p>
                </div>
            </div>
        </div>

        <!-- ACTION CARDS -->
        <div class="action-grid animate-stagger" style="margin-bottom: 32px;">

            <a href="/presensi" class="qr-card qr-card-interactive" style="display: block; padding: 32px; text-decoration: none; color: inherit;">
                <div style="font-size: 40px; margin-bottom: 16px;">📱</div>
                <h3 style="font-size: 20px; font-weight: 700; color: var(--color-primary); margin: 0 0 8px 0;">Scan Presensi</h3>
                <p style="color: var(--color-text-secondary); font-size: 14px; line-height: 1.6; margin: 0 0 20px 0;">
                    Arahkan kamera ke QR Code untuk mencatat kehadiran Anda.
                </p>
                <span class="qr-btn qr-btn-primary qr-btn-sm">Buka Scanner</span>
            </a>

            <a href="/riwayat-absensi" class="qr-card qr-card-interactive" style="display: block; padding: 32px; text-decoration: none; color: inherit;">
                <div style="font-size: 40px; margin-bottom: 16px;">📋</div>
                <h3 style="font-size: 20px; font-weight: 700; color: var(--color-primary); margin: 0 0 8px 0;">Riwayat Absensi</h3>
                <p style="color: var(--color-text-secondary); font-size: 14px; line-height: 1.6; margin: 0 0 20px 0;">
                    Lihat riwayat lengkap kehadiran Anda di semua kelas.
                </p>
                <span class="qr-btn qr-btn-primary qr-btn-sm">Lihat Detail</span>
            </a>

        </div>

        <!-- STATS -->
        <div class="stats-grid animate-stagger">
            <div class="qr-stat-card">
                <div class="qr-stat-icon" style="background: var(--color-success-light);">✅</div>
                <div class="qr-stat-value" style="color: var(--color-success);">{totalHadir}</div>
                <div class="qr-stat-label">Hadir</div>
            </div>

            <div class="qr-stat-card">
                <div class="qr-stat-icon" style="background: var(--color-warning-light);">⚠️</div>
                <div class="qr-stat-value" style="color: var(--color-warning);">{totalTelat}</div>
                <div class="qr-stat-label">Telat</div>
            </div>

            <div class="qr-stat-card">
                <div class="qr-stat-icon" style="background: var(--color-info-light);">ℹ️</div>
                <div class="qr-stat-value" style="color: var(--color-info);">{totalIzin}</div>
                <div class="qr-stat-label">Izin</div>
            </div>

            <div class="qr-stat-card">
                <div class="qr-stat-icon" style="background: var(--color-danger-light);">❌</div>
                <div class="qr-stat-value" style="color: var(--color-danger);">{totalAlpha}</div>
                <div class="qr-stat-label">Alpha</div>
            </div>
        </div>

    </main>

</div>

<style>
    .layout {
        min-height: 100vh;
        display: flex;
        background: var(--color-bg);
    }

    .topbar {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 32px;
        gap: 16px;
        flex-wrap: wrap;
    }

    .topbar-user { display: none; }

    .profile-row {
        display: flex;
        align-items: center;
        gap: 24px;
    }

    .profile-avatar {
        width: 72px;
        height: 72px;
        min-width: 72px;
        background: linear-gradient(135deg, #062B66, #0a3d8f);
        border-radius: var(--radius-lg);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 32px;
        color: white;
        font-weight: 800;
    }

    .action-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 20px;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 16px;
    }

    @media (min-width: 768px) {
        .topbar-user { display: flex; align-items: center; gap: 16px; }
        .action-grid { grid-template-columns: repeat(2, 1fr); }
        .stats-grid { grid-template-columns: repeat(4, 1fr); gap: 20px; }
    }

    @media (min-width: 1024px) {
        .qr-main { padding: 40px !important; }
    }
</style>