<script>
    import QRCode from 'qrcode'
    import { onMount, onDestroy } from 'svelte'
    import { router } from '@inertiajs/svelte'

    export let user
    export let mataKuliahs = []
    export let activeSesi = null

    let selectedMatkul = ''
    let durasi = 10
    let qrDataUrl = ''
    let countdown = ''
    let countdownInterval = null
    let pollingInterval = null
    let loading = false
    let kehadiranList = activeSesi?.kehadirans || []

    // Generate QR code saat ada sesi aktif
    async function generateQR(token) {
        qrDataUrl = await QRCode.toDataURL(token, {
            width: 320,
            margin: 2,
            color: {
                dark: '#062B66',
                light: '#FFFFFF',
            },
        })
    }

    // Countdown timer
    function startCountdown() {
        if (!activeSesi) return

        countdownInterval = setInterval(() => {
            const now = new Date()
            const expiry = new Date(activeSesi.expired_at)
            const diff = expiry - now

            if (diff <= 0) {
                countdown = '00:00'
                clearInterval(countdownInterval)
                clearInterval(pollingInterval)
                activeSesi = null
                qrDataUrl = ''
                return
            }

            const min = Math.floor(diff / 60000)
            const sec = Math.floor((diff % 60000) / 1000)
            countdown = `${String(min).padStart(2, '0')}:${String(sec).padStart(2, '0')}`
        }, 1000)
    }

    // Polling status sesi
    function startPolling() {
        if (!activeSesi) return

        pollingInterval = setInterval(async () => {
            try {
                const res = await fetch(`/presensi/${activeSesi.id}/status`)
                const data = await res.json()

                kehadiranList = data.kehadirans || []

                if (!data.is_active) {
                    clearInterval(pollingInterval)
                    clearInterval(countdownInterval)
                    activeSesi = null
                    qrDataUrl = ''
                    countdown = ''
                }
            } catch (e) {
                console.error('Polling error:', e)
            }
        }, 3000)
    }

    // Buka sesi baru
    function bukaSesi() {
        if (!selectedMatkul) {
            alert('Pilih mata kuliah terlebih dahulu!')
            return
        }

        loading = true

        router.post('/presensi', {
            mata_kuliah_id: selectedMatkul,
            durasi_menit: durasi,
        }, {
            onFinish: () => {
                loading = false
            }
        })
    }

    // Tutup sesi
    function tutupSesi() {
        router.post(`/presensi/${activeSesi.id}/close`)
    }

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

    let csrfToken = ''

    onMount(async () => {
        csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || ''
        if (activeSesi) {
            await generateQR(activeSesi.token)
            kehadiranList = activeSesi.kehadirans || []
            startCountdown()
            startPolling()
        }
    })

    onDestroy(() => {
        if (countdownInterval) clearInterval(countdownInterval)
        if (pollingInterval) clearInterval(pollingInterval)
    })
</script>

<div class="min-h-screen bg-slate-100 flex">

    <!-- SIDEBAR -->
    <aside class="w-72 bg-white border-r border-slate-200 p-6 flex flex-col">

        <div class="mb-10">
            <h1 class="text-3xl font-bold text-[#062B66]">
                QR-Attend
            </h1>
        </div>

        <nav class="space-y-3 flex-1">

            <a
                href="/dosen/dashboard"
                class="flex items-center gap-3 text-slate-600 hover:bg-slate-100 px-5 py-4 rounded-xl"
            >
                📊 Dashboard
            </a>

            <a
                href="/mahasiswa"
                class="flex items-center gap-3 text-slate-600 hover:bg-slate-100 px-5 py-4 rounded-xl"
            >
                👨‍🎓 Data Mahasiswa
            </a>

            <a
                href="/presensi"
                class="flex items-center gap-3 bg-slate-100 text-[#062B66] px-5 py-4 rounded-xl font-semibold"
            >
                📷 Sesi Presensi
            </a>

            <a
                href="/rekap"
                class="flex items-center gap-3 text-slate-600 hover:bg-slate-100 px-5 py-4 rounded-xl"
            >
                📄 Rekap Kehadiran
            </a>

        </nav>

    </aside>

    <!-- CONTENT -->
    <main class="flex-1 p-10 overflow-auto">

        <!-- TOPBAR -->
        <div class="flex justify-between items-center mb-10">

            <div>
                <h1 class="text-5xl font-bold text-[#062B66] mb-2">
                    Sesi Presensi
                </h1>
                <p class="text-slate-500 text-xl">
                    Buka sesi absensi dan tampilkan QR Code untuk mahasiswa.
                </p>
            </div>

            <div class="flex items-center gap-5">
                <div class="text-right">
                    <h2 class="font-bold text-slate-800 text-lg">{user.name}</h2>
                    <p class="text-slate-500 capitalize">{user.role}</p>
                </div>
                <button
                    onclick={logout}
                    class="bg-white shadow px-5 py-3 rounded-xl border border-slate-200 hover:bg-red-50"
                >
                    Logout
                </button>
            </div>

        </div>

        {#if activeSesi}

            <!-- SESI AKTIF -->
            <div class="bg-white rounded-3xl shadow-sm p-10 mb-8 border border-slate-200">

                <div class="flex items-start justify-between gap-10">

                    <div class="flex-1">

                        <div class="flex items-center gap-3 mb-6">
                            <span class="relative flex h-3 w-3">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                            </span>
                            <span class="bg-green-100 text-green-700 px-4 py-2 rounded-xl text-sm font-semibold">
                                Sesi Aktif
                            </span>
                        </div>

                        <h2 class="text-4xl font-bold text-[#062B66] mb-3">
                            {activeSesi.mata_kuliah?.nama_matkul || 'Mata Kuliah'}
                        </h2>

                        <p class="text-slate-500 text-lg mb-8">
                            Minta mahasiswa memindai QR Code di samping untuk mencatat kehadiran.
                        </p>

                        <!-- COUNTDOWN -->
                        <div class="bg-slate-50 rounded-2xl p-6 mb-6 border border-slate-200">
                            <p class="text-slate-500 text-sm mb-2">Sisa Waktu</p>
                            <p class="text-5xl font-bold text-[#062B66] font-mono">
                                {countdown || '--:--'}
                            </p>
                        </div>

                        <div class="flex gap-4">
                            <div class="bg-blue-50 rounded-xl px-6 py-4 border border-blue-200">
                                <p class="text-blue-600 text-sm">Durasi</p>
                                <p class="text-2xl font-bold text-blue-700">{activeSesi.durasi_menit} menit</p>
                            </div>
                            <div class="bg-emerald-50 rounded-xl px-6 py-4 border border-emerald-200">
                                <p class="text-emerald-600 text-sm">Sudah Scan</p>
                                <p class="text-2xl font-bold text-emerald-700">{kehadiranList.length} orang</p>
                            </div>
                        </div>

                    </div>

                    <!-- QR CODE -->
                    <div class="flex flex-col items-center gap-4">
                        {#if qrDataUrl}
                            <div class="bg-white p-4 rounded-2xl shadow-lg border-2 border-[#062B66]">
                                <img src={qrDataUrl} alt="QR Code Presensi" class="w-72 h-72" />
                            </div>
                        {:else}
                            <div class="w-72 h-72 border-4 border-dashed border-[#062B66] rounded-2xl flex items-center justify-center text-slate-400">
                                Generating QR...
                            </div>
                        {/if}

                        <form action="/presensi/{activeSesi.id}/close" method="POST" class="w-full">
                            <input type="hidden" name="_token" value={csrfToken} />
                            <button
                                type="submit"
                                class="bg-red-500 hover:bg-red-600 text-white px-8 py-3 rounded-xl font-semibold transition-colors w-full"
                            >
                                ✕ Tutup Sesi
                            </button>
                        </form>
                    </div>

                </div>

            </div>

            <!-- DAFTAR KEHADIRAN -->
            <div class="bg-white rounded-3xl shadow-sm p-8 border border-slate-200">
                <h3 class="text-2xl font-bold text-[#062B66] mb-6">
                    Daftar Kehadiran ({kehadiranList.length})
                </h3>

                {#if kehadiranList.length === 0}
                    <p class="text-slate-400 text-center py-10 text-lg">
                        Belum ada mahasiswa yang melakukan scan.
                    </p>
                {:else}
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-slate-200">
                                    <th class="text-left py-3 px-4 text-slate-500 font-semibold">#</th>
                                    <th class="text-left py-3 px-4 text-slate-500 font-semibold">Nama</th>
                                    <th class="text-left py-3 px-4 text-slate-500 font-semibold">NIM</th>
                                    <th class="text-left py-3 px-4 text-slate-500 font-semibold">Waktu Scan</th>
                                    <th class="text-left py-3 px-4 text-slate-500 font-semibold">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                {#each kehadiranList as item, i}
                                    <tr class="border-b border-slate-100 hover:bg-slate-50">
                                        <td class="py-3 px-4 text-slate-600">{i + 1}</td>
                                        <td class="py-3 px-4 font-medium text-slate-800">{item.user_name || item.user?.name}</td>
                                        <td class="py-3 px-4 text-slate-600">{item.user_nim || item.user?.nim || '-'}</td>
                                        <td class="py-3 px-4 text-slate-600">
                                            {new Date(item.waktu_scan).toLocaleTimeString('id-ID')}
                                        </td>
                                        <td class="py-3 px-4">
                                            {#if item.status === 'hadir'}
                                                <span class="bg-green-100 text-green-700 px-3 py-1 rounded-lg text-sm font-semibold">
                                                    Hadir
                                                </span>
                                            {:else if item.status === 'telat'}
                                                <span class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded-lg text-sm font-semibold">
                                                    Telat
                                                </span>
                                            {:else}
                                                <span class="bg-slate-100 text-slate-600 px-3 py-1 rounded-lg text-sm font-semibold">
                                                    {item.status}
                                                </span>
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
            <div class="bg-white rounded-3xl shadow-sm p-10 border border-slate-200 max-w-2xl">

                <h2 class="text-3xl font-bold text-[#062B66] mb-2">
                    Buka Sesi Baru
                </h2>

                <p class="text-slate-500 mb-8">
                    Pilih mata kuliah dan atur durasi sesi absensi (maks. 15 menit).
                </p>

                <!-- MATA KULIAH -->
                <label class="block mb-2 text-slate-700 font-semibold">Mata Kuliah</label>
                <select
                    bind:value={selectedMatkul}
                    class="w-full border border-slate-300 p-4 rounded-xl mb-6 bg-white text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#062B66]"
                >
                    <option value="">-- Pilih Mata Kuliah --</option>
                    {#each mataKuliahs as mk}
                        <option value={mk.id}>{mk.nama_matkul}</option>
                    {/each}
                </select>

                <!-- DURASI -->
                <label class="block mb-2 text-slate-700 font-semibold">
                    Durasi Sesi: <span class="text-[#062B66]">{durasi} menit</span>
                </label>
                <input
                    type="range"
                    bind:value={durasi}
                    min="1"
                    max="15"
                    class="w-full mb-2 accent-[#062B66]"
                />
                <div class="flex justify-between text-sm text-slate-400 mb-8">
                    <span>1 menit</span>
                    <span>15 menit</span>
                </div>

                <p class="text-sm text-slate-400 mb-6">
                    ⏱ Mahasiswa yang scan setelah {durasi} menit akan dicatat sebagai <strong class="text-yellow-600">Telat</strong>.
                </p>

                <!-- BUTTON -->
                <button
                    onclick={bukaSesi}
                    disabled={loading}
                    class="bg-[#062B66] hover:bg-[#0a3d8f] text-white px-8 py-4 rounded-xl font-semibold text-lg transition-colors disabled:opacity-50 w-full"
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
