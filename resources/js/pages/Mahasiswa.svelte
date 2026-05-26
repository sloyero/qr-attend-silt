<script>
    export let mahasiswa = [];
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
                class="flex items-center gap-3 bg-slate-100 text-[#062B66] px-5 py-4 rounded-xl font-semibold"
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
                <h1 class="text-5xl font-bold text-[#062B66] mb-2">
                    Data Mahasiswa
                </h1>
                <p class="text-slate-500 text-lg">
                    Daftar mahasiswa di kelas Anda
                </p>
            </div>
        </div>

        <!-- TABLE -->
        <div class="bg-white rounded-3xl shadow-sm overflow-hidden border border-slate-200">
            <div class="p-8">
                <h2 class="text-2xl font-bold text-[#062B66] mb-6">
                    Daftar Mahasiswa
                </h2>

                {#if mahasiswa.length === 0}
                    <div class="text-center py-10">
                        <p class="text-slate-400 text-lg">
                            Belum ada data mahasiswa
                        </p>
                    </div>
                {:else}
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-slate-100 border-b border-slate-200">
                                <tr>
                                    <th class="p-4 text-left text-slate-600 font-semibold">No</th>
                                    <th class="p-4 text-left text-slate-600 font-semibold">NIM</th>
                                    <th class="p-4 text-left text-slate-600 font-semibold">Nama Mahasiswa</th>
                                    <th class="p-4 text-left text-slate-600 font-semibold">Email</th>
                                </tr>
                            </thead>
                            <tbody>
                                {#each mahasiswa as m, index}
                                    <tr class="border-b border-slate-200 hover:bg-slate-50">
                                        <td class="p-4 text-slate-600">{index + 1}</td>
                                        <td class="p-4 font-medium text-slate-700">{m.nim}</td>
                                        <td class="p-4 text-slate-600">{m.name}</td>
                                        <td class="p-4 text-slate-500">{m.email}</td>
                                    </tr>
                                {/each}
                            </tbody>
                        </table>
                    </div>
                {/if}
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
