<script>

    import { onMount } from 'svelte'

    export let user
    export let kehadirans = []

    let totalHadir = 0
    let totalTelat = 0
    let totalIzin = 0
    let totalAlpha = 0

    /*
    |--------------------------------------------------------------------------
    | LOGOUT
    |--------------------------------------------------------------------------
    */

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

    /*
    |--------------------------------------------------------------------------
    | COUNT ABSENSI
    |--------------------------------------------------------------------------
    */

    onMount(() => {

        if (Array.isArray(kehadirans)) {

            totalHadir = kehadirans.filter(k => k.status === 'hadir').length

            totalTelat = kehadirans.filter(k => k.status === 'telat').length

            totalIzin = kehadirans.filter(k => k.status === 'izin').length

            totalAlpha = kehadirans.filter(k => k.status === 'alpha').length

        }

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

            <!-- DASHBOARD -->
            <a
                href="/mahasiswa/dashboard"
                class="flex items-center gap-3 bg-slate-100 text-[#062B66] px-5 py-4 rounded-xl font-semibold"
            >
                📊 Dashboard
            </a>

            <!-- SCAN -->
            <a
                href="/presensi"
                class="flex items-center gap-3 text-slate-600 hover:bg-slate-100 px-5 py-4 rounded-xl"
            >
                🎯 Scan Presensi
            </a>

            <!-- RIWAYAT -->
            <a
                href="/riwayat-absensi"
                class="flex items-center gap-3 text-slate-600 hover:bg-slate-100 px-5 py-4 rounded-xl"
            >
                📄 Riwayat Absensi
            </a>

            <!-- PROFILE -->
            <a
                href="/akun"
                class="flex items-center gap-3 text-slate-600 hover:bg-slate-100 px-5 py-4 rounded-xl"
            >
                ⚙️ Profile
            </a>

        </nav>

        <!-- LOGOUT -->
        <button
            on:click={logout}
            class="w-full bg-red-50 text-red-600 hover:bg-red-100 px-5 py-3 rounded-xl font-semibold transition border border-red-200"
        >
            🚪 Logout
        </button>

    </aside>

    <!-- CONTENT -->
    <main class="flex-1 p-10 overflow-auto">

        <!-- TOPBAR -->
        <div class="flex justify-between items-center mb-10">

            <div>

                <h1 class="text-6xl font-bold text-[#062B66] mb-2">
                    Dashboard Mahasiswa
                </h1>

                <p class="text-slate-500 text-xl">
                    Pantau kehadiran Anda dan scan QR Code kelas.
                </p>

            </div>

            <div class="text-right">

                <h2 class="font-bold text-slate-800 text-lg">
                    {user.name}
                </h2>

                <p class="text-slate-500 text-sm">
                    {user.nim}
                </p>

                <p class="text-slate-500 capitalize text-sm">
                    {user.role}
                </p>

            </div>

        </div>

        <!-- PROFILE CARD -->
        <div class="bg-white rounded-3xl shadow-sm p-8 mb-10 border border-slate-200">

            <div class="flex items-center gap-8">

                <!-- AVATAR -->
                <div class="w-24 h-24 bg-linear-to-br from-[#062B66] to-[#0a3d8f] rounded-2xl flex items-center justify-center text-5xl text-white font-bold">

                    {user.name.charAt(0).toUpperCase()}

                </div>

                <!-- USER INFO -->
                <div>

                    <h2 class="text-3xl font-bold text-[#062B66] mb-2">
                        {user.name}
                    </h2>

                    <p class="text-slate-600 text-lg mb-1">

                        <span class="font-semibold">
                            NIM:
                        </span>

                        {user.nim || '-'}

                    </p>

                    <p class="text-slate-600 text-lg">

                        <span class="font-semibold">
                            Email:
                        </span>

                        {user.email}

                    </p>

                </div>

            </div>

        </div>

        <!-- ACTION -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-10">

            <!-- SCAN -->
            <a
                href="/presensi"
                class="bg-white rounded-3xl shadow-sm p-8 border border-slate-200 hover:shadow-lg hover:border-[#062B66] transition cursor-pointer"
            >

                <div class="text-6xl mb-4">
                    📱
                </div>

                <h3 class="text-2xl font-bold text-[#062B66] mb-2">
                    Scan Presensi
                </h3>

                <p class="text-slate-500 text-lg leading-relaxed">
                    Arahkan kamera ke QR Code untuk mencatat kehadiran Anda pada sesi yang aktif.
                </p>

                <div class="mt-6">

                    <span class="bg-[#062B66] text-white px-5 py-2 rounded-xl inline-block font-semibold">

                        Buka Scanner

                    </span>

                </div>

            </a>

            <!-- RIWAYAT -->
            <a
                href="/riwayat-absensi"
                class="bg-white rounded-3xl shadow-sm p-8 border border-slate-200 hover:shadow-lg hover:border-[#062B66] transition cursor-pointer"
            >

                <div class="text-6xl mb-4">
                    📋
                </div>

                <h3 class="text-2xl font-bold text-[#062B66] mb-2">
                    Riwayat Absensi
                </h3>

                <p class="text-slate-500 text-lg leading-relaxed">
                    Lihat riwayat lengkap kehadiran Anda di semua kelas dengan status dan waktu scan.
                </p>

                <div class="mt-6">

                    <span class="bg-[#062B66] text-white px-5 py-2 rounded-xl inline-block font-semibold">

                        Lihat Detail

                    </span>

                </div>

            </a>

        </div>

        <!-- STATS -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">

            <!-- HADIR -->
            <div class="bg-green-50 border border-green-200 p-6 rounded-3xl shadow-sm">

                <div class="text-4xl mb-3">
                    ✅
                </div>

                <h3 class="text-3xl font-bold text-green-700 mb-1">
                    {totalHadir}
                </h3>

                <p class="text-green-600 text-sm">
                    Hadir
                </p>

            </div>

            <!-- TELAT -->
            <div class="bg-yellow-50 border border-yellow-200 p-6 rounded-3xl shadow-sm">

                <div class="text-4xl mb-3">
                    ⚠️
                </div>

                <h3 class="text-3xl font-bold text-yellow-700 mb-1">
                    {totalTelat}
                </h3>

                <p class="text-yellow-600 text-sm">
                    Telat
                </p>

            </div>

            <!-- IZIN -->
            <div class="bg-blue-50 border border-blue-200 p-6 rounded-3xl shadow-sm">

                <div class="text-4xl mb-3">
                    ℹ️
                </div>

                <h3 class="text-3xl font-bold text-blue-700 mb-1">
                    {totalIzin}
                </h3>

                <p class="text-blue-600 text-sm">
                    Izin
                </p>

            </div>

            <!-- ALPHA -->
            <div class="bg-red-50 border border-red-200 p-6 rounded-3xl shadow-sm">

                <div class="text-4xl mb-3">
                    ❌
                </div>

                <h3 class="text-3xl font-bold text-red-700 mb-1">
                    {totalAlpha}
                </h3>

                <p class="text-red-600 text-sm">
                    Alpha
                </p>

            </div>

        </div>

    </main>

</div>

<style>

    :global(body) {

        margin: 0;
        padding: 0;

    }

</style>