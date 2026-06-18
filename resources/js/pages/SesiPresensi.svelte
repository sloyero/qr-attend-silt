<script>
    import QRCode from "qrcode";
    import { onMount, onDestroy } from "svelte";
    import { router } from "@inertiajs/svelte";
    import { toast } from "../stores/toastStore.js";

    export let user;
    export let mataKuliahs = [];
    export let activeSesi = null;

    let selectedMatkul = "";
    let durasi = 10;
    let qrDataUrl = "";
    let countdown = "";
    let countdownInterval = null;
    let pollingInterval = null;
    let loading = false;
    let kehadiranList = activeSesi?.kehadirans || [];
    let sidebarOpen = false;

    async function generateQR(token) {
        qrDataUrl = await QRCode.toDataURL(token, {
            width: 320,
            margin: 2,
            color: { dark: "#062B66", light: "#FFFFFF" },
        });
    }

    function startCountdown() {
        if (!activeSesi) return;
        countdownInterval = setInterval(() => {
            const now = new Date();
            const expiry = new Date(activeSesi.expired_at);
            const diff = expiry - now;
            if (diff <= 0) {
                countdown = "00:00";
                clearInterval(countdownInterval);
                clearInterval(pollingInterval);
                activeSesi = null;
                qrDataUrl = "";
                return;
            }
            const min = Math.floor(diff / 60000);
            const sec = Math.floor((diff % 60000) / 1000);
            countdown = `${String(min).padStart(2, "0")}:${String(sec).padStart(2, "0")}`;
        }, 1000);
    }

    function startPolling() {
        if (!activeSesi) return;
        pollingInterval = setInterval(async () => {
            try {
                const res = await fetch(`/presensi/${activeSesi.id}/status`);
                const data = await res.json();
                kehadiranList = data.kehadirans || [];
                if (!data.is_active) {
                    clearInterval(pollingInterval);
                    clearInterval(countdownInterval);
                    activeSesi = null;
                    qrDataUrl = "";
                    countdown = "";
                }
            } catch (e) {
                console.error("Polling error:", e);
            }
        }, 3000);
    }

    function bukaSesi() {
        if (!selectedMatkul) {
            toast.warning("Pilih mata kuliah terlebih dahulu!");
            return;
        }
        loading = true;
        router.post(
            "/presensi",
            {
                mata_kuliah_id: selectedMatkul,
                durasi_menit: durasi,
            },
            {
                onFinish: () => {
                    loading = false;
                },
            },
        );
    }

    function tutupSesi() {
        router.post(`/presensi/${activeSesi.id}/close`);
    }

    async function logout() {
        await fetch("/logout", {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": document.querySelector(
                    'meta[name="csrf-token"]',
                ).content,
                "Content-Type": "application/json",
            },
        });
        window.location.href = "/login";
    }

    let csrfToken = "";

    function toggleSidebar() {
        sidebarOpen = !sidebarOpen;
    }
    function closeSidebar() {
        sidebarOpen = false;
    }

    onMount(async () => {
        csrfToken =
            document.querySelector('meta[name="csrf-token"]')?.content || "";
        if (activeSesi) {
            await generateQR(activeSesi.token);
            kehadiranList = activeSesi.kehadirans || [];
            startCountdown();
            startPolling();
        }
    });

    onDestroy(() => {
        if (countdownInterval) clearInterval(countdownInterval);
        if (pollingInterval) clearInterval(pollingInterval);
    });
</script>

<div class="layout">
    <!-- HAMBURGER -->
    <button
        class="qr-hamburger"
        on:click={toggleSidebar}
        aria-label="Toggle menu"
    >
        {#if sidebarOpen}✕{:else}☰{/if}
    </button>

    <!-- svelte-ignore a11y_click_events_have_key_events -->
    <!-- svelte-ignore a11y_no_static_element_interactions -->
    <div
        class="qr-sidebar-overlay"
        class:open={sidebarOpen}
        on:click={closeSidebar}
    ></div>

    <!-- SIDEBAR -->
    <aside class="qr-sidebar" class:open={sidebarOpen}>
        <div style="margin-bottom: 40px;">
            <h1
                style="font-size: 24px; font-weight: 800; color: var(--color-primary); margin: 0;"
            >
                QR-Attend
            </h1>
        </div>
        <nav style="display: flex; flex-direction: column; gap: 4px; flex: 1;">
            <a
                href="/dosen/dashboard"
                class="qr-nav-link"
                on:click={closeSidebar}>📊 Dashboard</a
            >
            <a href="/mahasiswa" class="qr-nav-link" on:click={closeSidebar}
                >👨‍🎓 Data Mahasiswa</a
            >
            <a
                href="/presensi"
                class="qr-nav-link active"
                on:click={closeSidebar}>📷 Sesi Presensi</a
            >
            <a href="/rekap" class="qr-nav-link" on:click={closeSidebar}
                >📄 Rekap Kehadiran</a
            >
        </nav>
    </aside>

    <!-- CONTENT -->
    <main
        class="qr-main"
        style="flex: 1; padding: 32px 24px; overflow-y: auto;"
    >
        <!-- TOPBAR -->
        <div class="topbar animate-fade-in">
            <div>
                <h1 class="qr-page-title">Sesi Presensi</h1>
                <p class="qr-page-subtitle">
                    Buka sesi absensi dan tampilkan QR Code untuk mahasiswa.
                </p>
            </div>
            <div class="topbar-user">
                <div style="text-align: right;">
                    <h2
                        style="font-weight: 700; color: var(--color-text); font-size: 15px; margin: 0;"
                    >
                        {user.name}
                    </h2>
                    <p
                        style="color: var(--color-text-muted); font-size: 13px; text-transform: capitalize; margin: 2px 0 0 0;"
                    >
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
                <button
                    on:click={logout}
                    class="qr-btn qr-btn-outline qr-btn-sm">Logout</button
                >
            </div>
        </div>

        {#if activeSesi}
            <!-- SESI AKTIF -->
            <div
                class="qr-card animate-slide-up"
                style="padding: 32px; margin-bottom: 24px;"
            >
                <div class="sesi-layout">
                    <div style="flex: 1;">
                        <div
                            style="display: flex; align-items: center; gap: 10px; margin-bottom: 20px;"
                        >
                            <span class="live-dot"
                                ><span class="live-dot-ping"></span><span
                                    class="live-dot-core"
                                ></span></span
                            >
                            <span class="qr-badge qr-badge-success"
                                >Sesi Aktif</span
                            >
                        </div>

                        <h2
                            style="font-size: 24px; font-weight: 800; color: var(--color-primary); margin: 0 0 8px 0;"
                        >
                            {activeSesi.mata_kuliah?.nama_matkul ||
                                "Mata Kuliah"}
                        </h2>

                        <p
                            style="color: var(--color-text-secondary); font-size: 15px; margin: 0 0 24px 0;"
                        >
                            Minta mahasiswa memindai QR Code untuk mencatat
                            kehadiran.
                        </p>

                        <!-- COUNTDOWN -->
                        <div
                            style="background: var(--color-bg); border-radius: var(--radius-lg); padding: 20px; margin-bottom: 20px; border: 1px solid var(--color-border);"
                        >
                            <p
                                style="color: var(--color-text-muted); font-size: 13px; margin: 0 0 4px 0;"
                            >
                                Sisa Waktu
                            </p>
                            <p
                                style="font-size: 40px; font-weight: 800; color: var(--color-primary); margin: 0; font-family: 'Inter', monospace; letter-spacing: 0.05em;"
                            >
                                {countdown || "--:--"}
                            </p>
                        </div>

                        <div style="display: flex; gap: 12px; flex-wrap: wrap;">
                            <div
                                style="background: var(--color-info-light); border-radius: var(--radius-md); padding: 12px 20px; border: 1px solid #bfdbfe;"
                            >
                                <p
                                    style="color: var(--color-info); font-size: 12px; margin: 0;"
                                >
                                    Durasi
                                </p>
                                <p
                                    style="font-size: 20px; font-weight: 800; color: #1d4ed8; margin: 4px 0 0 0;"
                                >
                                    {activeSesi.durasi_menit} menit
                                </p>
                            </div>
                            <div
                                style="background: var(--color-success-light); border-radius: var(--radius-md); padding: 12px 20px; border: 1px solid #a7f3d0;"
                            >
                                <p
                                    style="color: var(--color-success); font-size: 12px; margin: 0;"
                                >
                                    Sudah Scan
                                </p>
                                <p
                                    style="font-size: 20px; font-weight: 800; color: #047857; margin: 4px 0 0 0;"
                                >
                                    {kehadiranList.length} orang
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- QR CODE -->
                    <div
                        style="display: flex; flex-direction: column; align-items: center; gap: 16px;"
                    >
                        {#if qrDataUrl}
                            <div class="qr-display">
                                <img
                                    src={qrDataUrl}
                                    alt="QR Code Presensi"
                                    style="width: 256px; height: 256px; border-radius: var(--radius-sm);"
                                />
                            </div>
                        {:else}
                            <div class="qr-placeholder-lg">
                                Generating QR...
                            </div>
                        {/if}

                        <form
                            action="/presensi/{activeSesi.id}/close"
                            method="POST"
                            style="width: 100%;"
                        >
                            <input
                                type="hidden"
                                name="_token"
                                value={csrfToken}
                            />
                            <button
                                type="submit"
                                class="qr-btn qr-btn-danger"
                                style="width: 100%;"
                            >
                                ✕ Tutup Sesi
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- DAFTAR KEHADIRAN -->
            <div class="qr-card" style="padding: 32px;">
                <h3
                    style="font-size: 20px; font-weight: 700; color: var(--color-primary); margin: 0 0 20px 0;"
                >
                    Daftar Kehadiran ({kehadiranList.length})
                </h3>

                {#if kehadiranList.length === 0}
                    <div class="qr-empty">
                        <div class="qr-empty-icon">📋</div>
                        <div class="qr-empty-text">
                            Belum ada mahasiswa yang melakukan scan.
                        </div>
                    </div>
                {:else}
                    <div
                        style="overflow-x: auto; border-radius: var(--radius-md); border: 1px solid var(--color-border);"
                    >
                        <table class="qr-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nama</th>
                                    <th>NIM</th>
                                    <th>Waktu Scan</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                {#each kehadiranList as item, i}
                                    <tr>
                                        <td>{i + 1}</td>
                                        <td style="font-weight: 600;"
                                            >{item.user_name ||
                                                item.user?.name}</td
                                        >
                                        <td
                                            >{item.user_nim ||
                                                item.user?.nim ||
                                                "-"}</td
                                        >
                                        <td
                                            style="color: var(--color-text-secondary);"
                                        >
                                            {new Date(
                                                item.waktu_scan,
                                            ).toLocaleTimeString("id-ID")}
                                        </td>
                                        <td>
                                            {#if item.status === "hadir"}
                                                <span
                                                    class="qr-badge qr-badge-success"
                                                    >Hadir</span
                                                >
                                            {:else if item.status === "telat"}
                                                <span
                                                    class="qr-badge qr-badge-warning"
                                                    >Telat</span
                                                >
                                            {:else}
                                                <span
                                                    class="qr-badge qr-badge-neutral"
                                                    >{item.status}</span
                                                >
                                            {/if}
                                        </td>
                                    </tr>
                                {/each}
                            </tbody>
                        </table>
                    </div>
                {/if}
            </div>
        {:else}
            <!-- FORM BUKA SESI -->
            <div
                class="qr-card animate-slide-up"
                style="padding: 40px 32px; max-width: 640px;"
            >
                <h2
                    style="font-size: 24px; font-weight: 800; color: var(--color-primary); margin: 0 0 8px 0;"
                >
                    Buka Sesi Baru
                </h2>

                <p
                    style="color: var(--color-text-secondary); font-size: 15px; margin: 0 0 32px 0;"
                >
                    Pilih mata kuliah dan atur durasi sesi absensi (maks. 15
                    menit).
                </p>

                <!-- MATA KULIAH -->
                <label for="matkul" class="qr-label">Mata Kuliah</label>
                <select
                    id="matkul"
                    bind:value={selectedMatkul}
                    class="qr-input"
                    style="margin-bottom: 24px; cursor: pointer;"
                >
                    <option value="">-- Pilih Mata Kuliah --</option>
                    {#each mataKuliahs as mk}
                        <option value={mk.id}>{mk.nama_matkul}</option>
                    {/each}
                </select>

                <!-- DURASI -->
                <label for="durasi" class="qr-label">
                    Durasi Sesi: <span
                        style="color: var(--color-primary); font-weight: 800;"
                        >{durasi} menit</span
                    >
                </label>
                <input
                    id="durasi"
                    type="range"
                    bind:value={durasi}
                    min="1"
                    max="15"
                    class="range-slider"
                />
                <div
                    style="display: flex; justify-content: space-between; font-size: 12px; color: var(--color-text-muted); margin-bottom: 24px;"
                >
                    <span>1 menit</span>
                    <span>15 menit</span>
                </div>

                <!-- BUTTON -->
                <button
                    on:click={bukaSesi}
                    disabled={loading}
                    class="qr-btn qr-btn-primary qr-btn-xl"
                    style="width: 100%;"
                >
                    {#if loading}
                        Membuka Sesi...
                    {:else}
                        📷 Buka Sesi Presensi
                    {/if}
                </button>
            </div>
        {/if}
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

    .sesi-layout {
        display: flex;
        flex-direction: column;
        gap: 32px;
    }

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
        75%,
        100% {
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

    .qr-placeholder-lg {
        width: 256px;
        height: 256px;
        border: 3px dashed var(--color-border);
        border-radius: var(--radius-xl);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--color-text-muted);
        font-size: 14px;
        font-weight: 600;
        background: var(--color-bg);
    }

    .range-slider {
        width: 100%;
        height: 6px;
        appearance: none;
        background: var(--color-border);
        border-radius: 3px;
        outline: none;
        margin-bottom: 8px;
        cursor: pointer;
    }

    .range-slider::-webkit-slider-thumb {
        appearance: none;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background: var(--color-primary);
        cursor: pointer;
        border: 3px solid white;
        box-shadow: var(--shadow-md);
    }

    .range-slider::-moz-range-thumb {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background: var(--color-primary);
        cursor: pointer;
        border: 3px solid white;
        box-shadow: var(--shadow-md);
    }

    @media (min-width: 768px) {
        .topbar-user {
            display: flex;
            align-items: center;
            gap: 16px;
        }
        .sesi-layout {
            flex-direction: row;
            align-items: flex-start;
        }
    }

    @media (min-width: 1024px) {
        .qr-main {
            padding: 40px !important;
        }
    }
</style>
