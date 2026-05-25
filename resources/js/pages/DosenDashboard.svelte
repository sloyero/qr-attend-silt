<script>
    import QRCode from 'qrcode'
    import { onMount } from 'svelte'

    export let user
    export let activeSesi = null
    export let totalMahasiswa = 0
    export let totalSesi = 0
    export let kehadiranRate = 95

    let qrDataUrl = ''

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
                class="flex items-center gap-3 bg-slate-100 text-[#062B66] px-5 py-4 rounded-xl font-semibold"
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
                class="flex items-center gap-3 text-slate-600 hover:bg-slate-100 px-5 py-4 rounded-xl"
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

                <h1 class="text-6xl font-bold text-[#062B66] mb-2">
                    Dashboard Dosen
                </h1>

                <p class="text-slate-500 text-xl">
                    Kelola presensi mahasiswa dan sesi QR.
                </p>

            </div>

            <div class="flex items-center gap-5">

                <div class="text-right">

                    <h2 class="font-bold text-slate-800 text-lg">
                        {user.name}
                    </h2>

                    <p class="text-slate-500 capitalize">
                        {user.role}
                    </p>

                </div>

                <button
                    on:click={logout}
                    class="bg-white shadow px-5 py-3 rounded-xl border border-slate-200 hover:bg-red-50"
                >
                    Logout
                </button>

            </div>

        </div>

        <!-- HERO / SESI AKTIF -->
        <div class="bg-white rounded-3xl shadow-sm p-10 mb-10 flex items-center justify-between border border-slate-200">

            <div class="max-w-xl">
                {#if activeSesi}
                    <div class="flex items-center gap-3 mb-6">
                        <span class="relative flex h-3 w-3">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                        </span>
                        <span class="bg-green-100 text-green-700 px-4 py-2 rounded-xl text-sm font-semibold">
                            Sesi Sedang Berjalan
                        </span>
                    </div>

                    <h2 class="text-5xl font-bold text-[#062B66] mb-5">
                        {activeSesi.mata_kuliah?.nama_matkul || 'Mata Kuliah'}
                    </h2>

                    <p class="text-slate-500 text-xl leading-relaxed mb-8">
                        Dosen sedang membuka sesi presensi secara realtime. Mahasiswa dapat memindai QR Code untuk mencatat kehadiran.
                    </p>

                    <a
                        href="/presensi"
                        class="inline-block bg-[#062B66] hover:bg-[#0a3d8f] text-white px-8 py-4 rounded-xl font-semibold text-lg transition-colors"
                    >
                        📷 Kelola Sesi Presensi
                    </a>
                {:else}
                    <span class="bg-slate-100 text-slate-600 px-4 py-2 rounded-xl text-sm font-semibold">
                        Sesi Tidak Aktif
                    </span>

                    <h2 class="text-5xl font-bold text-[#062B66] mt-6 mb-5">
                        Belum Ada Sesi Aktif
                    </h2>

                    <p class="text-slate-500 text-xl leading-relaxed mb-8">
                        Mulai kelas Anda dengan membuat QR Code presensi mahasiswa secara realtime.
                    </p>

                    <a
                        href="/presensi"
                        class="inline-block bg-[#062B66] hover:bg-[#0a3d8f] text-white px-8 py-4 rounded-xl font-semibold text-lg transition-colors"
                    >
                        📷 Buka Sesi Baru
                    </a>
                {/if}
            </div>

            <div class="flex flex-col items-center gap-4">
                {#if activeSesi && qrDataUrl}
                    <div class="bg-white p-4 rounded-2xl shadow-lg border-2 border-[#062B66]">
                        <img src={qrDataUrl} alt="QR Code Presensi" class="w-60 h-60" />
                    </div>
                {:else}
                    <div class="w-60 h-60 border-4 border-dashed border-slate-300 rounded-3xl flex items-center justify-center text-center text-slate-400 text-lg bg-slate-50">
                        QR CODE
                    </div>
                {/if}
            </div>

        </div>

        <!-- STATS -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-200">
                <div class="text-5xl mb-5">
                    👨‍🎓
                </div>
                <h1 class="text-5xl font-bold text-[#062B66] mb-3">
                    {totalMahasiswa}
                </h1>
                <p class="text-slate-500 text-lg">
                    Total Mahasiswa
                </p>
            </div>

            <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-200">
                <div class="text-5xl mb-5">
                    📷
                </div>
                <h1 class="text-5xl font-bold text-[#062B66] mb-3">
                    {totalSesi}
                </h1>
                <p class="text-slate-500 text-lg">
                    Total Sesi Kelas
                </p>
            </div>

            <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-200">
                <div class="text-5xl mb-5">
                    ✅
                </div>
                <h1 class="text-5xl font-bold text-[#062B66] mb-3">
                    {kehadiranRate}%
                </h1>
                <p class="text-slate-500 text-lg">
                    Rata-rata Kehadiran
                </p>
            </div>

        </div>

    </main>

</div>