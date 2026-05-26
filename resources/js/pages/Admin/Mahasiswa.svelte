<script>

    export let mahasiswa = []

</script>

<div class="min-h-screen bg-slate-900 text-white p-10">

    <!-- HEADER -->
    <div class="flex justify-between items-center mb-10">

        <div>

            <h1 class="text-5xl font-bold mb-2">
                Data Mahasiswa
            </h1>

            <p class="text-slate-400 text-lg">
                Daftar mahasiswa yang mengikuti mata kuliah
            </p>

        </div>

        <!-- BUTTON KEMBALI -->
        <a
            href="/dosen/dashboard"
            class="bg-blue-600 hover:bg-blue-700 px-6 py-3 rounded-xl transition"
        >
            Dashboard
        </a>

    </div>

    <!-- TABLE -->
    <div class="bg-slate-800 p-8 rounded-3xl shadow-xl">

        <h2 class="text-3xl font-bold mb-8">
            List Mahasiswa
        </h2>

        <table class="w-full">

            <thead>

                <tr class="text-left border-b border-slate-600">

                    <th class="p-4">
                        NIM
                    </th>

                    <th class="p-4">
                        Nama
                    </th>

                    <th class="p-4">
                        Email
                    </th>

                </tr>

            </thead>

            <tbody>

                {#each mahasiswa as m}

                    <tr class="border-b border-slate-700 hover:bg-slate-700 transition">

                        <td class="p-4">
                            {m.nim}
                        </td>

                        <td class="p-4">
                            {m.name}
                        </td>

                        <td class="p-4">
                            {m.email}
                        </td>

                    </tr>

                {/each}

            </tbody>

        </table>

    </div>

</div>
