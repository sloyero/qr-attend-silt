<script>
    import { toast } from '../stores/toastStore.js'
    import { router } from '@inertiajs/svelte'

    export let mahasiswa = []

    let name = ''
    let nim = ''
    let email = ''
    let password = ''
    let loading = false

    async function tambahMahasiswa() {
        if (!name || !nim || !email || !password) {
            toast.warning('Semua field wajib diisi!')
            return
        }

        loading = true
        try {
            const response = await fetch('/admin/mahasiswa', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({ name, nim, email, password }),
            })

            if (response.ok) {
                toast.success(`Mahasiswa ${name} berhasil ditambahkan! 🎓`)
                name = ''
                nim = ''
                email = ''
                password = ''
                router.reload({ only: ['mahasiswa'] })
            } else {
                const data = await response.json().catch(() => ({}))
                toast.error(data.message || 'Gagal menambahkan mahasiswa')
            }
        } catch (error) {
            toast.error('Terjadi kesalahan jaringan')
        } finally {
            loading = false
        }
    }

    async function hapusMahasiswa(id, name) {
        if (!confirm(`Apakah Anda yakin ingin menghapus mahasiswa ${name}?`)) return
        try {
            const response = await fetch(`/mahasiswa/${id}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
            })
            if (response.ok) {
                toast.success('Mahasiswa berhasil dihapus!')
                router.reload({ only: ['mahasiswa'] })
            } else {
                toast.error('Gagal menghapus mahasiswa')
            }
        } catch (error) {
            toast.error('Terjadi kesalahan jaringan')
        }
    }
</script>

<div class="admin-page">

    <!-- TOP BAR -->
    <header class="admin-topbar animate-fade-in">
        <div style="max-width: 1200px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
            <div>
                <div class="admin-label"><span>🛡️</span> Admin Portal</div>
                <h1 class="qr-page-title">Kelola Data Mahasiswa</h1>
            </div>
            <a href="/admin/dashboard" class="qr-btn qr-btn-outline qr-btn-sm">← Dashboard Admin</a>
        </div>
    </header>

    <!-- MAIN -->
    <main class="admin-body">

        <!-- FORM COLUMN -->
        <section class="form-col">
            <div class="qr-card animate-slide-up" style="padding: 24px; position: sticky; top: 96px;">
                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 24px;">
                    <div class="form-icon" style="background: var(--color-success-light);">🎓</div>
                    <div>
                        <h2 style="font-size: 18px; font-weight: 700; color: var(--color-primary); margin: 0;">Tambah Mahasiswa</h2>
                        <p style="font-size: 12px; color: var(--color-text-muted); margin: 2px 0 0 0;">Masukkan detail untuk akun mahasiswa baru</p>
                    </div>
                </div>

                <div style="display: flex; flex-direction: column; gap: 16px;">
                    <div>
                        <label for="name" class="qr-label">Nama Lengkap</label>
                        <input id="name" bind:value={name} type="text" placeholder="Contoh: Wildan Pratama" class="qr-input" />
                    </div>
                    <div>
                        <label for="nim" class="qr-label">NIM</label>
                        <input id="nim" bind:value={nim} type="text" placeholder="Contoh: 231110027" class="qr-input" />
                    </div>
                    <div>
                        <label for="email" class="qr-label">Alamat Email</label>
                        <input id="email" bind:value={email} type="email" placeholder="mahasiswa@universitas.ac.id" class="qr-input" />
                    </div>
                    <div>
                        <label for="password" class="qr-label">Password Akun</label>
                        <input id="password" bind:value={password} type="password" placeholder="••••••••" class="qr-input" />
                    </div>

                    <button on:click={tambahMahasiswa} disabled={loading} class="qr-btn qr-btn-primary" style="width: 100%; margin-top: 8px;">
                        {#if loading}
                            ⏳ Menyimpan...
                        {:else}
                            🎓 Daftarkan Mahasiswa
                        {/if}
                    </button>
                </div>
            </div>
        </section>

        <!-- TABLE COLUMN -->
        <section class="table-col">
            <div class="qr-card animate-slide-up" style="padding: 24px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 8px;">
                    <div>
                        <h2 style="font-size: 18px; font-weight: 700; color: var(--color-primary); margin: 0;">Daftar Mahasiswa Aktif</h2>
                        <p style="font-size: 12px; color: var(--color-text-muted); margin: 2px 0 0 0;">Total {mahasiswa.length} mahasiswa terdaftar</p>
                    </div>
                </div>

                <div style="overflow-x: auto; border-radius: var(--radius-md); border: 1px solid var(--color-border);">
                    <table class="qr-table">
                        <thead>
                            <tr>
                                <th>Info Mahasiswa</th>
                                <th style="width: 120px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            {#if mahasiswa.length === 0}
                                <tr>
                                    <td colspan="2">
                                        <div class="qr-empty">
                                            <div class="qr-empty-icon">🎓</div>
                                            <div class="qr-empty-text">Belum ada data mahasiswa terdaftar.</div>
                                        </div>
                                    </td>
                                </tr>
                            {:else}
                                {#each mahasiswa as m}
                                    <tr>
                                        <td>
                                            <div style="display: flex; align-items: center; gap: 12px;">
                                                <div class="table-avatar">{m.name.charAt(0).toUpperCase()}</div>
                                                <div>
                                                    <div style="font-weight: 700; color: var(--color-text);">{m.name}</div>
                                                    <div style="font-size: 12px; color: var(--color-text-muted); margin-top: 2px;">
                                                        NIM: {m.nim || '-'} <span style="color: var(--color-border);">•</span> {m.email}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <button on:click={() => hapusMahasiswa(m.id, m.name)} class="qr-btn qr-btn-ghost qr-btn-sm" style="color: var(--color-danger);">
                                                🗑️ Hapus
                                            </button>
                                        </td>
                                    </tr>
                                {/each}
                            {/if}
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

    </main>

</div>

<style>
    .admin-page {
        min-height: 100vh;
        background: var(--color-bg);
        display: flex;
        flex-direction: column;
    }

    .admin-topbar {
        background: rgba(255,255,255,0.85);
        backdrop-filter: blur(12px);
        border-bottom: 1px solid var(--color-border);
        position: sticky;
        top: 0;
        z-index: 10;
        padding: 20px 24px;
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
        margin-bottom: 4px;
    }

    .admin-body {
        flex: 1;
        max-width: 1200px;
        width: 100%;
        margin: 0 auto;
        padding: 32px 24px;
        display: grid;
        grid-template-columns: 1fr;
        gap: 24px;
        align-items: start;
    }

    .form-icon {
        width: 40px;
        height: 40px;
        border-radius: var(--radius-md);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
    }

    .table-avatar {
        width: 36px;
        height: 36px;
        min-width: 36px;
        border-radius: 50%;
        background: var(--color-primary-light);
        color: var(--color-primary);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 14px;
    }

    @media (min-width: 1024px) {
        .admin-body {
            grid-template-columns: 1fr 2fr;
            padding: 40px 32px;
        }
    }
</style>