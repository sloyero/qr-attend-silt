<script>
    export let kehadirans = [];
</script>

<div class="min-h-screen bg-slate-100 p-10">

    <!-- HEADER -->
    <div class="mb-10">

        <a
            href="/dosen/dashboard"
            class="inline-flex items-center gap-2 bg-[#062B66] text-white px-5 py-3 rounded-2xl mb-6 hover:bg-blue-900 transition"
        >
            ← Kembali ke Dashboard
        </a>


        <div class="flex justify-between items-center mb-4">
            <h1 class="text-6xl font-bold text-[#062B66]">
                Rekap Kehadiran
            </h1>

            <a
                href="/kehadiran/export"
                class="bg-green-600 text-white px-5 py-3 rounded-xl font-semibold hover:bg-green-700"
            >
                📥 Download Excel
            </a>
        </div>

        <p class="text-slate-500 text-lg">
            Data kehadiran seluruh mahasiswa.
        </p>
    </div>

    <!-- TABLE -->
    <div class="bg-white rounded-3xl shadow-sm overflow-hidden border border-slate-200">

        <table class="w-full">

            <thead class="bg-slate-100 text-slate-700">
                <tr>
                    <th class="p-5 text-left">No</th>
                    <th class="p-5 text-left">Nama Mahasiswa</th>
                    <th class="p-5 text-left">Mata Kuliah</th>
                    <th class="p-5 text-left">Waktu Scan</th>
                    <th class="p-5 text-left">Status</th>
                </tr>
            </thead>

            <tbody>

                {#if kehadirans.length > 0}

                    {#each kehadirans as item, index}

                        <tr class="border-t border-slate-200 hover:bg-slate-50">

                            <td class="p-5">
                                {index + 1}
                            </td>

                            <td class="p-5 font-semibold text-slate-700">
                                {item.user?.name}
                            </td>

                            <td class="p-5">
                                {item.mata_kuliah?.nama_matkul}
                            </td>

                            <td class="p-5 text-slate-500">
                                {item.waktu_scan}
                            </td>

                            <td class="p-5">

                                <span
                                    class="px-4 py-2 rounded-xl text-sm font-semibold
                                    {item.status === 'hadir'
                                        ? 'bg-green-100 text-green-700'
                                        : item.status === 'telat'
                                        ? 'bg-yellow-100 text-yellow-700'
                                        : item.status === 'izin'
                                        ? 'bg-blue-100 text-blue-700'
                                        : 'bg-red-100 text-red-700'}"
                                >
                                    {item.status}
                                </span>

                            </td>

                        </tr>

                    {/each}

                {:else}

                    <tr>
                        <td colspan="5" class="p-10 text-center text-slate-500">
                            Belum ada data kehadiran.
                        </td>
                    </tr>

                {/if}

            </tbody>

        </table>

    </div>

</div>