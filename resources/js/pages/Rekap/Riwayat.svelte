<script>
    export let riwayat = [];
</script>

<div class="page-wrap">

    <!-- HEADER -->
    <div class="animate-fade-in" style="margin-bottom: 32px;">

        <a href="/mahasiswa/dashboard" class="qr-btn qr-btn-outline qr-btn-sm" style="margin-bottom: 20px; display: inline-flex;">
            ← Kembali ke Dashboard
        </a>

        <h1 class="qr-page-title">Riwayat Absensi</h1>
        <p class="qr-page-subtitle">Riwayat kehadiran Anda.</p>
    </div>

    <!-- TABLE -->
    <div class="qr-card animate-slide-up" style="overflow: hidden;">
        <div style="overflow-x: auto;">
            <table class="qr-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Mata Kuliah</th>
                        <th>Waktu Scan</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    {#if riwayat.length > 0}
                        {#each riwayat as item, index}
                            <tr>
                                <td>{index + 1}</td>
                                <td style="font-weight: 600;">{item.mata_kuliah?.nama_matkul}</td>
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
                            <td colspan="4">
                                <div class="qr-empty">
                                    <div class="qr-empty-icon">📄</div>
                                    <div class="qr-empty-text">Belum ada riwayat absensi.</div>
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