<script>
    import { toast } from '../../stores/toastStore.js'
    import { router } from '@inertiajs/svelte'

    export let dosen = []

    let name = ''
    let nidn = ''
    let matkul = ''
    let email = ''
    let password = ''
    let loading = false

    // State for inline edit
    let editingId = null
    let editingMatkulVal = ''

    async function tambahDosen() {
        if (!name || !email || !password) {
            toast.warning('Nama, email, dan password wajib diisi!')
            return
        }
        loading = true
        try {
            const response = await fetch('/admin/dosen', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({ name, nidn, matkul, email, password }),
            })
            if (response.ok) {
                toast.success(`Dosen ${name} berhasil ditambahkan! 🎓`)
                name = ''
                nidn = ''
                matkul = ''
                email = ''
                password = ''
                router.reload({ only: ['dosen'] })
            } else {
                const data = await response.json().catch(() => ({}))
                toast.error(data.message || 'Gagal menambahkan dosen')
            }
        } catch (error) {
            toast.error('Terjadi kesalahan jaringan')
        } finally {
            loading = false
        }
    }

    async function hapusDosen(id, name) {
        if (!confirm(`Apakah Anda yakin ingin menghapus dosen ${name}?`)) return
        try {
            const response = await fetch(`/admin/dosen/${id}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
            })
            if (response.ok) {
                toast.success('Dosen berhasil dihapus!')
                router.reload({ only: ['dosen'] })
            } else {
                toast.error('Gagal menghapus dosen')
            }
        } catch (error) {
            toast.error('Terjadi kesalahan jaringan')
        }
    }

    async function editMatkul(id) {
        try {
            const response = await fetch(`/admin/dosen/${id}/matkul`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({ matkul: editingMatkulVal }),
            })
            if (response.ok) {
                toast.success('Mata kuliah berhasil diperbarui! 📚')
                editingId = null
                router.reload({ only: ['dosen'] })
            } else {
                toast.error('Gagal memperbarui mata kuliah')
            }
        } catch (error) {
            toast.error('Terjadi kesalahan jaringan')
        }
    }

    function startEdit(d) {
        editingId = d.id
        editingMatkulVal = d.matkul || ''
    }

    function cancelEdit() {
        editingId = null
    }
</script>

<div class="admin-page">

    <!-- TOP BAR -->
    <header class="admin-topbar animate-fade-in">
        <div style="max-width: 1200px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
            <div>
                <div class="admin-label"><span>🛡️</span> Admin Portal</div>
                <h1 class="qr-page-title">Kelola Data Dosen</h1>
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
                    <div class="form-icon" style="background: var(--color-primary-light);">➕</div>
                    <div>
                        <h2 style="font-size: 18px; font-weight: 700; color: var(--color-primary); margin: 0;">Tambah Dosen</h2>
                        <p style="font-size: 12px; color: var(--color-text-muted); margin: 2px 0 0 0;">Masukkan detail untuk akun dosen baru</p>
                    </div>
                </div>

                <div style="display: flex; flex-direction: column; gap: 16px;">
                    <div>
                        <label for="name" class="qr-label">Nama Lengkap</label>
                        <input id="name" bind:value={name} type="text" placeholder="Contoh: Dr. Budi Santoso" class="qr-input" />
                    </div>
                    <div>
                        <label for="nidn" class="qr-label">NIDN</label>
                        <input id="nidn" bind:value={nidn} type="text" placeholder="Contoh: 0412088501" class="qr-input" />
                    </div>
                    <div>
                        <label for="matkul" class="qr-label">Mata Kuliah</label>
                        <textarea id="matkul" bind:value={matkul} placeholder="Masukkan mata kuliah dipisahkan koma. Contoh: Pemrograman Web, Basis Data" class="qr-input" style="height: 88px; resize: none;"></textarea>
                    </div>
                    <div>
                        <label for="email" class="qr-label">Alamat Email</label>
                        <input id="email" bind:value={email} type="email" placeholder="dosen@universitas.ac.id" class="qr-input" />
                    </div>
                    <div>
                        <label for="password" class="qr-label">Password Akun</label>
                        <input id="password" bind:value={password} type="password" placeholder="••••••••" class="qr-input" />
                    </div>

                    <button on:click={tambahDosen} disabled={loading} class="qr-btn qr-btn-primary" style="width: 100%; margin-top: 8px;">
                        {#if loading}
                            ⏳ Menyimpan...
                        {:else}
                            🎓 Daftarkan Dosen
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
                        <h2 style="font-size: 18px; font-weight: 700; color: var(--color-primary); margin: 0;">Daftar Dosen Aktif</h2>
                        <p style="font-size: 12px; color: var(--color-text-muted); margin: 2px 0 0 0;">Total {dosen.length} dosen terdaftar di sistem</p>
                    </div>
                </div>

                <div style="overflow-x: auto; border-radius: var(--radius-md); border: 1px solid var(--color-border);">
                    <table class="qr-table">
                        <thead>
                            <tr>
                                <th>Info Dosen</th>
                                <th>Mata Kuliah</th>
                                <th style="width: 100px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            {#if dosen.length === 0}
                                <tr>
                                    <td colspan="3">
                                        <div class="qr-empty">
                                            <div class="qr-empty-icon">👨‍🏫</div>
                                            <div class="qr-empty-text">Belum ada data dosen terdaftar.</div>
                                        </div>
                                    </td>
                                </tr>
                            {:else}
                                {#each dosen as d}
                                    <tr>
                                        <!-- INFO -->
                                        <td>
                                            <div style="display: flex; align-items: center; gap: 12px;">
                                                <div class="table-avatar">{d.name.charAt(0).toUpperCase()}</div>
                                                <div>
                                                    <div style="font-weight: 700; color: var(--color-text);">{d.name}</div>
                                                    <div style="font-size: 12px; color: var(--color-text-muted); margin-top: 2px;">
                                                        ID: {d.nidn || '-'} <span style="color: var(--color-border);">•</span> {d.email}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- MATA KULIAH / INLINE EDIT -->
                                        <td>
                                            {#if editingId === d.id}
                                                <div style="display: flex; align-items: center; gap: 8px; max-width: 280px;">
                                                    <input bind:value={editingMatkulVal} class="qr-input" style="padding: 8px 12px; font-size: 13px;" placeholder="Pisahkan dengan koma" />
                                                    <button on:click={() => editMatkul(d.id)} class="qr-btn qr-btn-sm" style="background: var(--color-success); color: white; padding: 8px 12px;" title="Simpan">✓</button>
                                                    <button on:click={cancelEdit} class="qr-btn qr-btn-ghost qr-btn-sm" title="Batal">✕</button>
                                                </div>
                                            {:else}
                                                <div class="matkul-cell">
                                                    <div style="display: flex; flex-wrap: wrap; gap: 4px;">
                                                        {#if d.matkul}
                                                            {#each d.matkul.split(',') as subject}
                                                                <span class="qr-badge qr-badge-info" style="font-size: 11px; padding: 2px 8px;">
                                                                    {subject.trim()}
                                                                </span>
                                                            {/each}
                                                        {:else}
                                                            <span style="font-size: 13px; color: var(--color-text-muted); font-style: italic;">Belum ada matkul</span>
                                                        {/if}
                                                    </div>
                                                    <button on:click={() => startEdit(d)} class="edit-btn qr-btn qr-btn-ghost qr-btn-sm" style="font-size: 12px;">
                                                        ✏️ Edit
                                                    </button>
                                                </div>
                                            {/if}
                                        </td>

                                        <!-- ACTIONS -->
                                        <td>
                                            <button on:click={() => hapusDosen(d.id, d.name)} class="qr-btn qr-btn-ghost qr-btn-sm" style="color: var(--color-danger);">
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

    .matkul-cell {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .edit-btn {
        opacity: 0;
        transition: opacity var(--transition-fast);
    }

    tr:hover .edit-btn {
        opacity: 1;
    }

    @media (min-width: 1024px) {
        .admin-body {
            grid-template-columns: 1fr 2fr;
            padding: 40px 32px;
        }
    }
</style>