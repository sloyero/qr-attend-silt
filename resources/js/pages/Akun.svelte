<script>
    import { toast } from '../stores/toastStore.js'
    import { router } from '@inertiajs/svelte'

    export let user

    let password = ''
    let fileInput
    let uploading = false
    let sidebarOpen = false

    $: capitalizedRole = user && user.role ? user.role.charAt(0).toUpperCase() + user.role.slice(1) : ''
    $: backUrl = user && user.role === 'admin' ? '/admin/dashboard' : (user && user.role === 'dosen' ? '/dosen/dashboard' : '/mahasiswa/dashboard')

    async function updatePassword() {
        if (!password) {
            toast.warning('Password tidak boleh kosong!')
            return
        }
        if (password.length < 6) {
            toast.warning('Password minimal 6 karakter!')
            return
        }
        try {
            const response = await fetch('/akun/password', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({ password }),
            })
            if (response.ok) {
                toast.success('Password berhasil diubah! 🔒')
                password = ''
            } else {
                const data = await response.json()
                toast.error(data.message || 'Gagal mengubah password')
            }
        } catch (error) {
            toast.error('Terjadi kesalahan saat mengubah password')
        }
    }

    async function handlePhotoUpload(event) {
        const file = event.target.files[0]
        if (!file) return
        if (file.size > 2 * 1024 * 1024) {
            toast.warning('Ukuran foto maksimal 2MB!')
            fileInput.value = ''
            return
        }
        const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg']
        if (!allowedTypes.includes(file.type)) {
            toast.warning('Format foto harus JPG/JPEG/PNG!')
            fileInput.value = ''
            return
        }
        uploading = true
        try {
            const formData = new FormData()
            formData.append('photo', file)
            const response = await fetch('/akun/photo', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                body: formData
            })
            if (response.ok) {
                toast.success('Foto profil berhasil diperbarui! 📸')
                router.reload({
                    only: ['user'],
                    onSuccess: () => { uploading = false },
                    onError: () => { uploading = false }
                })
            } else {
                const data = await response.json().catch(() => ({}))
                toast.error(data.message || 'Gagal mengunggah foto profil')
                uploading = false
            }
        } catch (error) {
            toast.error('Terjadi kesalahan jaringan saat mengunggah foto')
            uploading = false
        } finally {
            fileInput.value = ''
        }
    }

    function triggerFileInput() { if (fileInput) fileInput.click() }
    function toggleSidebar() { sidebarOpen = !sidebarOpen }
    function closeSidebar() { sidebarOpen = false }
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
            <a href={backUrl} class="qr-nav-link" on:click={closeSidebar}>📊 Dashboard</a>
            <a href="/akun" class="qr-nav-link active" on:click={closeSidebar}>⚙️ Profile</a>
        </nav>
    </aside>

    <main class="qr-main" style="flex: 1; padding: 32px 24px; overflow-y: auto;">

        <!-- TOPBAR -->
        <div class="animate-fade-in" style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 32px; gap: 16px; flex-wrap: wrap;">
            <div>
                <h1 class="qr-page-title">Profile {capitalizedRole}</h1>
                <p class="qr-page-subtitle">Kelola informasi pribadi, foto profil, dan kata sandi Anda.</p>
            </div>
            <a href={backUrl} class="qr-btn qr-btn-outline qr-btn-sm">← Kembali</a>
        </div>

        <div class="profile-grid">

            <!-- LEFT COLUMN -->
            <div style="display: flex; flex-direction: column; gap: 20px;">

                <!-- AVATAR CARD -->
                <div class="qr-card animate-slide-up" style="padding: 32px; display: flex; flex-direction: column; align-items: center;">

                    <!-- AVATAR -->
                    <div class="avatar-container">
                        {#if uploading}
                            <div class="avatar-overlay">
                                <svg class="avatar-spinner" viewBox="0 0 24 24" fill="none">
                                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" opacity="0.3"/>
                                    <path d="M12 2a10 10 0 0 1 10 10" stroke="currentColor" stroke-width="3" stroke-linecap="round"/>
                                </svg>
                            </div>
                        {/if}

                        <button on:click={triggerFileInput} type="button" class="avatar-edit-btn" aria-label="Ubah foto profil">
                            📸 Ubah
                        </button>

                        {#if user && user.photo}
                            <img src="/profile/{user.photo}" alt={user.name} class="avatar-img" />
                        {:else}
                            <div class="avatar-placeholder">
                                {user && user.name ? user.name.charAt(0).toUpperCase() : '?'}
                            </div>
                        {/if}
                    </div>

                    <input bind:this={fileInput} on:change={handlePhotoUpload} type="file" accept="image/*" style="display: none;" />

                    <button on:click={triggerFileInput} disabled={uploading} class="qr-btn qr-btn-primary qr-btn-sm" style="margin-top: 16px;">
                        📸 Unggah Foto Baru
                    </button>

                    <p style="font-size: 12px; color: var(--color-text-muted); text-align: center; margin: 12px 0 0 0;">
                        Maksimal 2MB. Format: JPG, JPEG, PNG.
                    </p>

                    <hr style="width: 100%; border: none; border-top: 1px solid var(--color-border); margin: 20px 0;" />

                    <span class="qr-badge qr-badge-info" style="text-transform: uppercase; letter-spacing: 0.05em;">
                        {user ? user.role : ''}
                    </span>
                </div>

                <!-- INFO CARD -->
                <div class="qr-card" style="padding: 32px;">
                    <h4 style="font-size: 12px; font-weight: 700; color: var(--color-text-muted); text-transform: uppercase; letter-spacing: 0.06em; margin: 0 0 16px 0;">Informasi Akun</h4>

                    {#if user}
                        {#if user.role === 'mahasiswa'}
                            <div class="info-item">
                                <p class="info-label">NIM</p>
                                <p class="info-value">{user.nim || '-'}</p>
                            </div>
                        {:else if user.role === 'dosen'}
                            <div class="info-item">
                                <p class="info-label">NIDN</p>
                                <p class="info-value">{user.nidn || '-'}</p>
                            </div>
                            {#if user.matkul}
                                <div class="info-item">
                                    <p class="info-label">Mata Kuliah</p>
                                    <p class="info-value">{user.matkul}</p>
                                </div>
                            {/if}
                        {/if}

                        <div class="info-item">
                            <p class="info-label">Nama Lengkap</p>
                            <p class="info-value">{user.name}</p>
                        </div>
                        <div class="info-item">
                            <p class="info-label">Email</p>
                            <p class="info-value" style="word-break: break-all;">{user.email}</p>
                        </div>
                    {/if}
                </div>
            </div>

            <!-- RIGHT COLUMN -->
            <div>
                <div class="qr-card animate-slide-up" style="padding: 32px; height: 100%;">

                    <h3 style="font-size: 20px; font-weight: 700; color: var(--color-primary); margin: 0 0 8px 0; display: flex; align-items: center; gap: 8px;">
                        <span>🔒</span> Ubah Password
                    </h3>
                    <p style="color: var(--color-text-secondary); margin: 0 0 32px 0; font-size: 14px; line-height: 1.6;">
                        Masukkan kata sandi baru Anda di bawah ini untuk memperbarui keamanan akun.
                    </p>

                    <div>
                        <label for="newPassword" class="qr-label">Password Baru</label>
                        <input
                            id="newPassword"
                            bind:value={password}
                            type="password"
                            placeholder="Masukkan password baru (min. 6 karakter)"
                            class="qr-input"
                        />
                    </div>

                    <button on:click={updatePassword} class="qr-btn qr-btn-primary" style="width: 100%; margin-top: 16px;">
                        Simpan Password
                    </button>

                    <!-- TIPS -->
                    <div style="margin-top: 32px; padding: 20px; background: var(--color-info-light); border: 1px solid #bfdbfe; border-radius: var(--radius-lg);">
                        <h5 style="color: #1e40af; font-weight: 700; font-size: 14px; margin: 0 0 8px 0; display: flex; align-items: center; gap: 6px;">
                            <span>💡</span> Tips Keamanan Akun:
                        </h5>
                        <ul style="color: #1d4ed8; font-size: 13px; margin: 0; padding: 0 0 0 20px; line-height: 1.8;">
                            <li>Gunakan minimal 6 karakter.</li>
                            <li>Jangan gunakan kata sandi yang sama dengan situs lain.</li>
                            <li>Perbarui kata sandi Anda secara berkala.</li>
                        </ul>
                    </div>

                </div>
            </div>

        </div>

    </main>

</div>

<style>
    .layout { min-height: 100vh; display: flex; background: var(--color-bg); }

    .profile-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 24px;
    }

    .avatar-container {
        position: relative;
        width: 128px;
        height: 128px;
    }

    .avatar-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 50%;
        border: 4px solid white;
        box-shadow: var(--shadow-md);
    }

    .avatar-placeholder {
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, #062B66, #0a3d8f);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 48px;
        color: white;
        font-weight: 800;
        border: 4px solid white;
        box-shadow: var(--shadow-md);
    }

    .avatar-overlay {
        position: absolute;
        inset: 0;
        background: rgba(255,255,255,0.7);
        backdrop-filter: blur(2px);
        border-radius: 50%;
        z-index: 10;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .avatar-spinner {
        width: 32px;
        height: 32px;
        color: var(--color-primary);
        animation: spin 0.8s linear infinite;
    }

    @keyframes spin { to { transform: rotate(360deg); } }

    .avatar-edit-btn {
        position: absolute;
        inset: 0;
        background: rgba(0,0,0,0.5);
        opacity: 0;
        border-radius: 50%;
        z-index: 5;
        transition: opacity var(--transition-base);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        border: none;
        font-family: inherit;
    }

    .avatar-container:hover .avatar-edit-btn {
        opacity: 1;
    }

    .info-item {
        padding: 16px;
        background: var(--color-bg);
        border-radius: var(--radius-md);
        border: 1px solid var(--color-border);
        margin-bottom: 12px;
    }

    .info-label {
        font-size: 12px;
        font-weight: 600;
        color: var(--color-text-muted);
        margin: 0 0 2px 0;
    }

    .info-value {
        font-size: 15px;
        font-weight: 700;
        color: var(--color-text);
        margin: 0;
    }

    @media (min-width: 1024px) {
        .profile-grid { grid-template-columns: 1fr 2fr; }
        .qr-main { padding: 40px !important; }
    }
</style>