<script>

    export let dosen = []

    let name = ''
    let nidn = ''
    let matkul = ''
    let email = ''
    let password = ''

    /*
    |--------------------------------------------------------------------------
    | TAMBAH DOSEN
    |--------------------------------------------------------------------------
    */

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

    /*
    |--------------------------------------------------------------------------
    | HAPUS DOSEN
    |--------------------------------------------------------------------------
    */

    async function hapusDosen(id) {

        await fetch(`/admin/dosen/${id}`, {

            method: 'DELETE',

            headers: {

                'X-CSRF-TOKEN': document
                    .querySelector('meta[name="csrf-token"]')
                    .content,

            },

        })

        window.location.reload()

    }

    /*
    |--------------------------------------------------------------------------
    | EDIT MATA KULIAH
    |--------------------------------------------------------------------------
    */

    async function editMatkul(id, matkulBaru) {

        await fetch(`/admin/dosen/${id}/matkul`, {

            method: 'PUT',

            headers: {

                'Content-Type': 'application/json',

                'X-CSRF-TOKEN': document
                    .querySelector('meta[name="csrf-token"]')
                    .content,

            },

            body: JSON.stringify({

                matkul: matkulBaru,

            }),

        })

        window.location.reload()

    }

</script>

<div class="min-h-screen bg-slate-900 text-white p-10">

    <!-- HEADER -->
    <div class="flex justify-between items-center mb-10">

        <div>

            <h1 class="text-5xl font-bold mb-2">
                Kelola Dosen
            </h1>

            <p class="text-slate-400 text-lg">
                Tambah dosen dan kelola mata kuliah
            </p>

        </div>

        <!-- BUTTON -->
        <a
            href="/admin/dashboard"
            class="bg-blue-600 hover:bg-blue-700 px-6 py-3 rounded-xl transition"
        >
            Dashboard Admin
        </a>

    </div>

    <!-- FORM -->
    <div class="bg-slate-800 p-8 rounded-3xl max-w-3xl mb-10">

        <h2 class="text-3xl font-bold mb-8">
            Tambah Dosen
        </h2>

        <!-- NAMA -->
        <input
            bind:value={name}
            type="text"
            placeholder="Nama Dosen"
            class="w-full p-4 rounded-xl text-black mb-4"
        />

        <!-- NIDN -->
        <input
            bind:value={nidn}
            type="text"
            placeholder="NIDN"
            class="w-full p-4 rounded-xl text-black mb-4"
        />

        <!-- MATKUL -->
        <textarea
            bind:value={matkul}
            placeholder="Masukkan mata kuliah dipisahkan koma

Contoh:
Pemrograman Web, Basis Data, AI"
            class="w-full p-4 rounded-xl text-black mb-4 h-32"
        ></textarea>

        <!-- EMAIL -->
        <input
            bind:value={email}
            type="email"
            placeholder="Email"
            class="w-full p-4 rounded-xl text-black mb-4"
        />

        <!-- PASSWORD -->
        <input
            bind:value={password}
            type="password"
            placeholder="Password"
            class="w-full p-4 rounded-xl text-black mb-6"
        />

        <!-- BUTTON -->
        <button
            on:click={tambahDosen}
            class="bg-blue-600 hover:bg-blue-700 px-6 py-4 rounded-xl transition"
        >
            Tambah Dosen
        </button>

    </div>

    <!-- TABLE -->
    <div class="bg-slate-800 p-8 rounded-3xl overflow-auto">

        <h2 class="text-3xl font-bold mb-8">
            Data Dosen
        </h2>

        <table class="w-full">

            <thead>

                <tr class="border-b border-slate-600">

                    <th class="text-left p-4">
                        Nama
                    </th>

                    <th class="text-left p-4">
                        NIDN
                    </th>

                    <th class="text-left p-4">
                        Mata Kuliah
                    </th>

                    <th class="text-left p-4">
                        Email
                    </th>

                    <th class="text-left p-4">
                        Aksi
                    </th>

                </tr>

            </thead>

            <tbody>

                {#each dosen as d}

                    <tr class="border-b border-slate-700 hover:bg-slate-700 transition">

                        <td class="p-4">
                            {d.name}
                        </td>

                        <td class="p-4">
                            {d.nidn}
                        </td>

                        <!-- EDIT MATKUL -->
                        <td class="p-4">

                            <div class="flex gap-3">

                                <input
                                    bind:value={d.matkul}
                                    class="text-black px-3 py-2 rounded-lg w-full"
                                />

                                <button
                                    on:click={() => editMatkul(d.id, d.matkul)}
                                    class="bg-yellow-500 hover:bg-yellow-600 px-4 py-2 rounded-lg"
                                >
                                    Save
                                </button>

                            </div>

                        </td>

                        <td class="p-4">
                            {d.email}
                        </td>

                        <!-- BUTTON HAPUS -->
                        <td class="p-4">

                            <button
                                on:click={() => hapusDosen(d.id)}
                                class="bg-red-600 hover:bg-red-700 px-4 py-2 rounded-xl transition"
                            >
                                Hapus
                            </button>

                        </td>

                    </tr>

                {/each}

            </tbody>

        </table>

    </div>

</div>