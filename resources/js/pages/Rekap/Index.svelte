<script>
    export let kehadirans = [];
</script>

<div class="page-wrap">

    <!-- HEADER -->
    <div class="animate-fade-in" style="margin-bottom: 32px;">

        <a href="/dosen/dashboard" class="qr-btn qr-btn-outline qr-btn-sm" style="margin-bottom: 20px; display: inline-flex;">
            ← Kembali ke Dashboard
        </a>

        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; flex-wrap: wrap; gap: 12px;">
            <h1 class="qr-page-title">Rekap Kehadiran</h1>
            <a href="/kehadiran/export" class="qr-btn qr-btn-sm" style="background: var(--color-success); color: white;">
                📥 Download Excel
            </a>
        </div>

        <p class="qr-page-subtitle">Data kehadiran seluruh mahasiswa.</p>
    </div>

    <!-- TABLE -->
    <div class="qr-card animate-slide-up" style="overflow: hidden;">
        <div style="overflow-x: auto;">
            <table class="qr-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Mahasiswa</th>
                        <th>Mata Kuliah</th>
                        <th>Waktu Scan</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    {#if kehadirans.length > 0}
                        {#each kehadirans as item, index}
                            <tr>
                                <td>{index + 1}</td>
                                <td style="font-weight: 600;">{item.user?.name}</td>
                                <td>{item.mata_kuliah?.nama_matkul}</td>
                                <td style="color: var(--color-text-secondary);">{item.waktu_scan}</td>
                                <td>
                                    <span class="qr-badge {item.status === 'hadir' ? 'qr-badge-success' : item.status === 'telat' ? 'qr-badge-warning' : item.status === 'izin' ? 'qr-badge-info' : 'qr-badge-danger'}">
                                        {item.status}
                                    </span>
                                </td>
                            </tr>
                        {/each}
                    {:else}
                        <tr>
                            <td colspan="5">
                                <div class="qr-empty">
                                    <div class="qr-empty-icon">📋</div>
                                    <div class="qr-empty-text">Belum ada data kehadiran.</div>
                                </div>
                            </td>
                        </tr>
                    {/if}
                </tbody>
            </table>
        </div>
    </div>

</div>

<style>
    .page-wrap {
        min-height: 100vh;
        background: var(--color-bg);
        padding: 32px 24px;
        max-width: 1200px;
        margin: 0 auto;
    }

    @media (min-width: 768px) {
        .page-wrap { padding: 48px 40px; }
    }
</style>