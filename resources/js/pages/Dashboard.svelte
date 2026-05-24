<script>

    export let user

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
                href="/dashboard"
                class="flex items-center gap-3 bg-slate-100 text-[#062B66] px-5 py-4 rounded-xl font-semibold"
            >
                📊 Dashboard
            </a>

            <a
                href="/akun"
                class="flex items-center gap-3 text-slate-600 hover:bg-slate-100 px-5 py-4 rounded-xl"
            >
                👤 Akun
            </a>
            
           {#if user.role === 'dosen'}

            <a
                href="/mahasiswa"
                class="flex items-center gap-3 text-slate-600 hover:bg-slate-100 px-5 py-4 rounded-xl"
            >
                👨‍🎓 Data Mahasiswa
            </a>

        {/if}

            <a
                href="/dashboard"
                class="flex items-center gap-3 text-slate-600 hover:bg-slate-100 px-5 py-4 rounded-xl"
            >
                📷 Sesi Presensi
            </a>

            <a
                href="/riwayat-absensi"
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
                    Dashboard
                </h1>

                <p class="text-slate-500 text-xl">
                    Ringkasan aktivitas dan kehadiran mahasiswa hari ini.
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

        <!-- HERO CARD -->
        <div class="bg-white rounded-3xl shadow-sm p-10 mb-10 flex items-center justify-between border border-slate-200">

            <div class="max-w-2xl">

                <span class="bg-blue-100 text-blue-700 px-4 py-2 rounded-xl text-sm font-semibold">
                    Sesi Sedang Berjalan
                </span>

                <h2 class="text-5xl font-bold text-[#062B66] mt-6 mb-5">
                    Pemrograman Web Lanjut
                </h2>

                <p class="text-slate-500 text-xl leading-relaxed">
                    Silakan minta mahasiswa memindai QR Code di bawah ini
                    untuk mencatat kehadiran. Sesi akan otomatis
                    berakhir dalam 10 menit.
                </p>

            </div>

            <div class="w-72 h-72 border-4 border-dashed border-[#062B66] rounded-3xl flex items-center justify-center text-center text-slate-500 text-lg">
                QR CODE
            </div>

        </div>

        <!-- STATS -->
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">

            <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-200">

                <div class="text-5xl mb-5">
                    👨‍🎓
                </div>

                <h1 class="text-5xl font-bold text-[#062B66] mb-3">
                    1,240
                </h1>

                <p class="text-slate-500 text-lg">
                    Total Mahasiswa
                </p>

            </div>

            <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-200">

                <div class="text-5xl mb-5">
                    ✅
                </div>

                <h1 class="text-5xl font-bold text-[#062B66] mb-3">
                    856
                </h1>

                <p class="text-slate-500 text-lg">
                    Hadir Hari Ini
                </p>

            </div>

            <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-200">

                <div class="text-5xl mb-5">
                    ⏰
                </div>

                <h1 class="text-5xl font-bold text-[#062B66] mb-3">
                    3
                </h1>

                <p class="text-slate-500 text-lg">
                    Sesi Aktif
                </p>

            </div>

            <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-200">

                <div class="text-5xl mb-5">
                    📈
                </div>

                <h1 class="text-5xl font-bold text-[#062B66] mb-3">
                    92%
                </h1>

                <p class="text-slate-500 text-lg">
                    Rata-rata Kehadiran
                </p>

            </div>

        </div>

    </main>

</div>