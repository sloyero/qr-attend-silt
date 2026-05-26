<script>

    export let dosen = []

    let name = ''
    let nidn = ''
    let matkul = ''
    let email = ''
    let password = ''

    async function tambahDosen() {

        await fetch('/admin/dosen', {

            method: 'POST',

            headers: {

                'Content-Type': 'application/json',

                'X-CSRF-TOKEN': document
                    .querySelector('meta[name="csrf-token"]')
                    .content,

            },

            body: JSON.stringify({

                name,
                nidn,
                matkul,
                email,
                password,

            }),

        })

        window.location.reload()

    }

</script>

<div class="min-h-screen bg-slate-100 p-10">

    <!-- HEADER -->
    <div class="flex justify-between items-center mb-10">

        <div>

            <h1 class="text-5xl font-bold text-[#062B66] mb-2">
                Kelola Dosen
            </h1>

            <p class="text-slate-500 text-lg">
                Tambah dan kelola data dosen
            </p>

        </div>

        <!-- BUTTON KEMBALI -->
        <a
            href="/admin/dashboard"
            class="bg-[#062B66] text-white px-6 py-3 rounded-xl"
        >
            Dashboard Admin
        </a>

    </div>

    <!-- FORM -->
    <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-200 mb-10 max-w-3xl">

        <h2 class="text-3xl font-bold text-[#062B66] mb-8">
            Tambah Dosen
        </h2>

        <!-- NAMA -->
        <input
            bind:value={name}
            placeholder="Nama Dosen"
            class="w-full border p-4 rounded-xl mb-4"
        />

        <!-- NIDN -->
        <input
            bind:value={nidn}
            placeholder="NIDN"
            class="w-full border p-4 rounded-xl mb-4"
        />

        <!-- MATKUL -->
        <textarea
            bind:value={matkul}
            placeholder="Masukkan mata kuliah dipisahkan koma

Contoh:
Pemrograman Web, Basis Data, AI"
            class="w-full border p-4 rounded-xl mb-4 h-32"
        ></textarea>

        <!-- EMAIL -->
        <input
            bind:value={email}
            placeholder="Email"
            class="w-full border p-4 rounded-xl mb-4"
        />

        <!-- PASSWORD -->
        <input
            bind:value={password}
            type="password"
            placeholder="Password"
            class="w-full border p-4 rounded-xl mb-6"
        />

        <!-- BUTTON -->
        <button
            on:click={tambahDosen}
            class="bg-[#062B66] text-white px-6 py-4 rounded-xl"
        >
            Tambah Dosen
        </button>

    </div>

    <!-- TABLE -->
    <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-200">

        <h2 class="text-3xl font-bold text-[#062B66] mb-8">
            Data Dosen
        </h2>

        <table class="w-full">

            <thead>

                <tr class="border-b">

                    <th class="p-4 text-left">
                        Nama
                    </th>

                    <th class="p-4 text-left">
                        NIDN
                    </th>

                    <th class="p-4 text-left">
                        Mata Kuliah
                    </th>

                    <th class="p-4 text-left">
                        Email
                    </th>

                </tr>

            </thead>

            <tbody>

                {#each dosen as d}

                    <tr class="border-b">

                        <td class="p-4">
                            {d.name}
                        </td>

                        <td class="p-4">
                            {d.nidn}
                        </td>

                        <td class="p-4">
                            {d.matkul}
                        </td>

                        <td class="p-4">
                            {d.email}
                        </td>

                    </tr>

                {/each}

            </tbody>

        </table>

    </div>

</div>