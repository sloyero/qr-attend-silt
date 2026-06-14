<script>
    import { Html5Qrcode } from "html5-qrcode";
    import { onDestroy } from "svelte";
    import { toast } from "../../stores/toastStore.js";

    export let user;

    let scanning = false;
    let scanner = null;
    let scanResult = null;
    let scanError = null;
    let scanLoading = false;
    let sidebarOpen = false;

    async function startScanner() {
        scanning = true;
        scanResult = null;
        scanError = null;
        await new Promise((r) => setTimeout(r, 100));
        scanner = new Html5Qrcode("qr-reader");
        try {
            await scanner.start(
                { facingMode: "environment" },
                { fps: 10, qrbox: { width: 250, height: 250 } },
                async (decodedText) => {
                    await stopScanner();
                    await kirimAbsensi(decodedText);
                },
                () => {},
            );
        } catch (err) {
            scanError = "Tidak dapat mengakses kamera.";
            toast.error("Tidak dapat mengakses kamera. Pastikan izin kamera sudah diberikan.");
            scanning = false;
        }
    }

    async function stopScanner() {
        if (scanner) {
            try { await scanner.stop(); } catch (e) {}
            scanner = null;
        }
        scanning = false;
    }

    async function kirimAbsensi(token) {
        scanLoading = true;
        scanResult = null;
        scanError = null;
        try {
            const res = await fetch("/absensi/scan", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({ token }),
            });
            const data = await res.json();
            if (data.success) {
                toast.success(data.message || "Presensi berhasil dicatat!");
                scanResult = { message: data.message, status: data.status };
            } else {
                toast.error(data.message || "Gagal mencatat presensi");
                scanError = data.message;
            }
        } catch (err) {
            scanError = "Terjadi kesalahan jaringan.";
            toast.error("Terjadi kesalahan jaringan.");
        }
        scanLoading = false;
    }

    async function logout() {
        await fetch("/logout", {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                "Content-Type": "application/json",
            },
        });
        window.location.href = "/login";
    }

    function toggleSidebar() { sidebarOpen = !sidebarOpen; }
    function closeSidebar() { sidebarOpen = false; }

    onDestroy(() => {
        if (scanner) { try { scanner.stop(); } catch (e) {} }
    });
</script>

<div class="layout">

    <button class="qr-hamburger" on:click={toggleSidebar} aria-label="Toggle menu">
        {#if sidebarOpen}✕{:else}☰{/if}
    </button>

    <!-- svelte-ignore a11y_click_events_have_key_events -->
    <!-- svelte-ignore a11y_no_static_element_interactions -->
    <div class="qr-sidebar-overlay" class:open={sidebarOpen} on:click={closeSidebar}></div>

    <aside class="qr-sidebar" class:open={sidebarOpen}>
        <div style="margin-bottom: 40px;">
            <h1 style="font-size: 24px; font-weight: 800; color: var(--color-primary); margin: 0;">QR-Attend</h1>
        </div>
        <nav style="display: flex; flex-direction: column; gap: 4px; flex: 1;">
            <a href="/mahasiswa/dashboard" class="qr-nav-link" on:click={closeSidebar}>📊 Dashboard</a>
            <a href="/presensi" class="qr-nav-link active" on:click={closeSidebar}>🎯 Scan Presensi</a>
            <a href="/riwayat-absensi" class="qr-nav-link" on:click={closeSidebar}>📄 Riwayat Absensi</a>
            <a href="/akun" class="qr-nav-link" on:click={closeSidebar}>⚙️ Profile</a>
        </nav>
        <button on:click={logout} class="qr-btn qr-btn-outline" style="width: 100%; margin-top: 16px; color: var(--color-danger);">🚪 Logout</button>
    </aside>

    <main class="qr-main" style="flex: 1; padding: 32px 24px; overflow-y: auto;">

        <div class="topbar animate-fade-in">
            <div>
                <h1 class="qr-page-title">Scan Presensi</h1>
                <p class="qr-page-subtitle">Selamat datang, {user.name}</p>
            </div>
        </div>

        <!-- SCAN QR -->
        <div class="qr-card animate-slide-up" style="padding: 40px 32px;">
            <div style="text-align: center; max-width: 480px; margin: 0 auto;">
                <div style="font-size: 48px; margin-bottom: 20px;">📷</div>

                <h2 style="font-size: 24px; font-weight: 800; color: var(--color-primary); margin: 0 0 8px 0;">
                    Scan QR Code Presensi
                </h2>

                <p style="color: var(--color-text-secondary); font-size: 15px; margin: 0 0 32px 0;">
                    Scan QR dari dosen untuk mencatat kehadiran
                </p>

                <!-- RESULT -->
                {#if scanResult}
                    <div style="margin-bottom: 24px; padding: 24px; border-radius: var(--radius-lg); background: var(--color-success-light); border: 1px solid #a7f3d0;">
                        <div style="font-size: 32px; margin-bottom: 8px;">✅</div>
                        <p style="font-size: 17px; font-weight: 700; color: var(--color-success); margin: 0;">
                            {scanResult.message}
                        </p>
                    </div>
                {/if}

                {#if scanError}
                    <div style="margin-bottom: 24px; padding: 24px; border-radius: var(--radius-lg); background: var(--color-danger-light); border: 1px solid #fca5a5;">
                        <div style="font-size: 32px; margin-bottom: 8px;">❌</div>
                        <p style="font-size: 15px; font-weight: 600; color: var(--color-danger); margin: 0;">
                            {scanError}
                        </p>
                    </div>
                {/if}

                <!-- QR READER -->
                {#if scanning}
                    <div style="margin-bottom: 24px;">
                        <div id="qr-reader" style="border-radius: var(--radius-lg); overflow: hidden; max-width: 400px; margin: 0 auto;"></div>
                    </div>
                    <button on:click={stopScanner} class="qr-btn qr-btn-danger qr-btn-lg">
                        Batal Scan
                    </button>
                {:else}
                    <button on:click={startScanner} class="qr-btn qr-btn-primary qr-btn-xl">
                        📷 Mulai Scan QR Code
                    </button>
                {/if}
            </div>
        </div>

    </main>

</div>

<style>
    .layout { min-height: 100vh; display: flex; background: var(--color-bg); }
    .topbar { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 32px; gap: 16px; flex-wrap: wrap; }
    @media (min-width: 1024px) { .qr-main { padding: 40px !important; } }
</style>
