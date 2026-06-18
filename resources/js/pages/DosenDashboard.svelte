<script>
    import QRCode from 'qrcode'
    import { onMount } from 'svelte'

    export let user
    export let activeSesi = null
    export let totalMahasiswa = 0
    export let totalSesi = 0
    export let kehadiranRate = 95

    let qrDataUrl = ''
    let sidebarOpen = false

    async function generateQR(token) {
        qrDataUrl = await QRCode.toDataURL(token, {
            width: 256,
            margin: 2,
            color: {
                dark: '#062B66',
                light: '#FFFFFF',
            },
        })
    }

    onMount(async () => {
        if (activeSesi) {
            await generateQR(activeSesi.token)
        }
    })

    async function logout() {

        await fetch('/logout', {

            method: 'POST',

            headers: {

                'X-CSRF-TOKEN': document
                    .querySelector('meta[name="csrf-token"]')
                    .content,

                'Content-Type': 'application/json',

            },

        })

        window.location.href = '/login'

    }

    function toggleSidebar() {
        sidebarOpen = !sidebarOpen
    }

    function closeSidebar() {
        sidebarOpen = false
    }

</script>

<div class="layout">

    <!-- HAMBURGER (mobile) -->
    <button class="qr-hamburger" on:click={toggleSidebar} aria-label="Toggle menu">
        {#if sidebarOpen}✕{:else}☰{/if}
    </button>

    <!-- SIDEBAR OVERLAY (mobile) -->
    <!-- svelte-ignore a11y_click_events_have_key_events -->
    <!-- svelte-ignore a11y_no_static_element_interactions -->
    <div class="qr-sidebar-overlay" class:open={sidebarOpen} on:click={closeSidebar}></div>

    <!-- SIDEBAR -->
    <aside class="qr-sidebar" class:open={sidebarOpen}>

        <div style="margin-bottom: 40px;">
            <h1 style="font-size: 24px; font-weight: 800; color: var(--color-primary); margin: 0; letter-spacing: -0.02em;">
                QR-Attend
            </h1>
        </div>

        <nav style="display: flex; flex-direction: column; gap: 4px; flex: 1;">

            <a href="/dosen/dashboard" class="qr-nav-link active" on:click={closeSidebar}>
                📊 Dashboard
            </a>

            <a href="/mahasiswa" class="qr-nav-link" on:click={closeSidebar}>
                👨‍🎓 Data Mahasiswa
            </a>

            <a href="/presensi" class="qr-nav-link" on:click={closeSidebar}>
                📷 Sesi Presensi
            </a>

            <a href="/rekap" class="qr-nav-link" on:click={closeSidebar}>
                📄 Rekap Kehadiran
            </a>

            <a href="/akun" class="qr-nav-link" on:click={closeSidebar}>
                ⚙️ Profile
            </a>

        </nav>

        <button
            on:click={logout}
            class="qr-btn qr-btn-outline"
            style="width: 100%; margin-top: 16px; color: var(--color-danger);"
        >
            🚪 Logout
        </button>

    </aside>

    <!-- CONTENT -->
    <main class="qr-main" style="flex: 1; padding: 32px 24px; overflow-y: auto;">

        <!-- TOPBAR -->
        <div class="topbar animate-fade-in">

            <div>
                <h1 class="qr-page-title">Dashboard Dosen</h1>
                <p class="qr-page-subtitle">Kelola presensi mahasiswa dan sesi QR.</p>
            </div>

            <div class="topbar-user">
                <div style="text-align: right;">
                    <h2 style="font-weight: 700; color: var(--color-text); font-size: 15px; margin: 0;">
                        {user.name}
                    </h2>
                    <p style="color: var(--color-text-muted); font-size: 13px; text-transform: capitalize; margin: 2px 0 0 0;">
                        {user.role}
                    </p>
                </div>
                {#if user && user.photo}
                    <img src="/profile/{user.photo}" alt={user.name} style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover; border: 2px solid white; box-shadow: var(--shadow-sm);" />
                {:else}
                    <div style="width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, #062B66, #0a3d8f); display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 16px; border: 2px solid white; box-shadow: var(--shadow-sm);">
                        {user && user.name ? user.name.charAt(0).toUpperCase() : '?'}
                    </div>
                {/if}
            </div>

        </div>

        <!-- HERO / SESI AKTIF -->
        <div class="hero-card qr-card animate-slide-up" style="padding: 40px 32px; margin-bottom: 32px;">

            <div class="hero-layout">

                <div style="flex: 1;">
                    {#if activeSesi}
                        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 24px;">
                            <span class="live-dot">
                                <span class="live-dot-ping"></span>
                                <span class="live-dot-core"></span>
                            </span>
                            <span class="qr-badge qr-badge-success">
                                Sesi Sedang Berjalan
                            </span>
                        </div>

                        <h2 style="font-size: 28px; font-weight: 800; color: var(--color-primary); margin: 0 0 12px 0; letter-spacing: -0.02em;">
                            {activeSesi.mata_kuliah?.nama_matkul || 'Mata Kuliah'}
                        </h2>

                        <p style="color: var(--color-text-secondary); font-size: 16px; line-height: 1.6; margin: 0 0 32px 0;">
                            Dosen sedang membuka sesi presensi secara realtime. Mahasiswa dapat memindai QR Code untuk mencatat kehadiran.
                        </p>

                        <a
                            href="/presensi"
                            class="qr-btn qr-btn-primary qr-btn-lg"
                        >
                            📷 Kelola Sesi Presensi
                        </a>
                    {:else}
                        <span class="qr-badge qr-badge-neutral" style="margin-bottom: 24px; display: inline-flex;">
                            Sesi Tidak Aktif
                        </span>

                        <h2 style="font-size: 28px; font-weight: 800; color: var(--color-primary); margin: 0 0 12px 0; letter-spacing: -0.02em;">
                            Belum Ada Sesi Aktif
                        </h2>

                        <p style="color: var(--color-text-secondary); font-size: 16px; line-height: 1.6; margin: 0 0 32px 0;">
                            Mulai kelas Anda dengan membuat QR Code presensi mahasiswa secara realtime.
                        </p>

                        <a
                            href="/presensi"
                            class="qr-btn qr-btn-primary qr-btn-lg"
                        >
                            📷 Buka Sesi Baru
                        </a>
                    {/if}
                </div>

                <div style="display: flex; flex-direction: column; align-items: center; gap: 16px;">
                    {#if activeSesi && qrDataUrl}
                        <div class="qr-display">
                            <img src={qrDataUrl} alt="QR Code Presensi" style="width: 220px; height: 220px; border-radius: var(--radius-md);" />
                        </div>
                    {:else}
                        <div class="qr-placeholder">
                            <span style="font-size: 32px; opacity: 0.4;">📷</span>
                            <span>QR CODE</span>
                        </div>
                    {/if}
                </div>

            </div>

        </div>

        <!-- STATS -->
        <div class="stats-grid animate-stagger">

            <div class="qr-stat-card">
                <div class="qr-stat-icon" style="background: var(--color-primary-light);">
                    👨‍🎓
                </div>
                <div class="qr-stat-value" style="color: var(--color-primary);">
                    {totalMahasiswa}
                </div>
                <div class="qr-stat-label">
                    Total Mahasiswa
                </div>
            </div>

            <div class="qr-stat-card">
                <div class="qr-stat-icon" style="background: var(--color-info-light);">
                    📷
                </div>
                <div class="qr-stat-value" style="color: var(--color-info);">
                    {totalSesi}
                </div>
                <div class="qr-stat-label">
                    Total Sesi Kelas
                </div>
            </div>

            <div class="qr-stat-card">
                <div class="qr-stat-icon" style="background: var(--color-success-light);">
                    ✅
                </div>
                <div class="qr-stat-value" style="color: var(--color-success);">
                    {kehadiranRate}%
                </div>
                <div class="qr-stat-label">
                    Rata-rata Kehadiran
                </div>
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

    .topbar-user {
        display: none;
    }

    .hero-layout {
        display: flex;
        flex-direction: column;
        gap: 32px;
    }

    /* Live indicator dot */
    .live-dot {
        position: relative;
        display: flex;
        width: 12px;
        height: 12px;
    }

    .live-dot-ping {
        position: absolute;
        inset: 0;
        border-radius: 50%;
        background: #34d399;
        opacity: 0.75;
        animation: ping 1.5s cubic-bezier(0, 0, 0.2, 1) infinite;
    }

    .live-dot-core {
        position: relative;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: #059669;
    }

    @keyframes ping {
        75%, 100% {
            transform: scale(2);
            opacity: 0;
        }
    }

    .qr-display {
        background: white;
        padding: 16px;
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-lg);
        border: 2px solid var(--color-primary);
    }

    .qr-placeholder {
        width: 220px;
        height: 220px;
        border: 3px dashed var(--color-border);
        border-radius: var(--radius-xl);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 8px;
        color: var(--color-text-muted);
        font-size: 14px;
        font-weight: 600;
        background: var(--color-bg);
    }

    .stats-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 16px;
    }

    /* Tablet */
    @media (min-width: 768px) {
        .topbar-user {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .hero-layout {
            flex-direction: row;
            align-items: flex-start;
        }

        .stats-grid {
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }
    }

    /* Desktop */
    @media (min-width: 1024px) {
        .qr-main {
            padding: 40px !important;
        }
    }
</style>