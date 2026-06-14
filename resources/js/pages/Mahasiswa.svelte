<script>
    export let mahasiswa = [];

    let sidebarOpen = false;
    function toggleSidebar() { sidebarOpen = !sidebarOpen; }
    function closeSidebar() { sidebarOpen = false; }
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
            <a href="/dosen/dashboard" class="qr-nav-link" on:click={closeSidebar}>📊 Dashboard</a>
            <a href="/mahasiswa" class="qr-nav-link active" on:click={closeSidebar}>👨‍🎓 Data Mahasiswa</a>
            <a href="/presensi" class="qr-nav-link" on:click={closeSidebar}>📷 Sesi Presensi</a>
            <a href="/rekap" class="qr-nav-link" on:click={closeSidebar}>📄 Rekap Kehadiran</a>
        </nav>
    </aside>

    <main class="qr-main" style="flex: 1; padding: 32px 24px; overflow-y: auto;">

        <div class="animate-fade-in" style="margin-bottom: 32px;">
            <h1 class="qr-page-title">Data Mahasiswa</h1>
            <p class="qr-page-subtitle">Daftar mahasiswa di kelas Anda</p>
        </div>

        <div class="qr-card animate-slide-up" style="padding: 32px;">
            <h2 style="font-size: 18px; font-weight: 700; color: var(--color-primary); margin: 0 0 20px 0;">
                Daftar Mahasiswa
            </h2>

            {#if mahasiswa.length === 0}
                <div class="qr-empty">
                    <div class="qr-empty-icon">👨‍🎓</div>
                    <div class="qr-empty-text">Belum ada data mahasiswa</div>
                </div>
            {:else}
                <div style="overflow-x: auto; border-radius: var(--radius-md); border: 1px solid var(--color-border);">
                    <table class="qr-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>NIM</th>
                                <th>Nama Mahasiswa</th>
                                <th>Email</th>
                            </tr>
                        </thead>
                        <tbody>
                            {#each mahasiswa as m, index}
                                <tr>
                                    <td>{index + 1}</td>
                                    <td style="font-weight: 600;">{m.nim}</td>
                                    <td>{m.name}</td>
                                    <td style="color: var(--color-text-secondary);">{m.email}</td>
                                </tr>
                            {/each}
                        </tbody>
                    </table>
                </div>
            {/if}
        </div>

    </main>

</div>

<style>
    .layout { min-height: 100vh; display: flex; background: var(--color-bg); }
    @media (min-width: 1024px) { .qr-main { padding: 40px !important; } }
</style>
