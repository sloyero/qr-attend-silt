<script>

    async function logout() {

        await fetch('/logout', {

            method: 'POST',

            headers: {

                'X-CSRF-TOKEN': document
                    .querySelector('meta[name="csrf-token"]')
                    .content,

            },

        })

        window.location.href = '/login'

    }

</script>

<div class="min-h-screen bg-slate-100 p-10">

    <!-- HEADER -->
    <div class="flex justify-between items-center mb-14">

        <div>

            <h1 class="text-6xl font-bold text-[#062B66] mb-3">
                Dashboard Admin
            </h1>

            <p class="text-slate-500 text-xl">
                Pilih menu pengelolaan sistem
            </p>

        </div>

        <button
            on:click={logout}
            class="bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-2xl text-lg"
        >
            Logout
        </button>

    </div>

    <!-- MENU -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">

        <!-- DOSEN -->
        <a
            href="/admin/dosen"
            class="bg-white rounded-3xl border border-slate-200 shadow-sm p-10 hover:scale-105 transition duration-300"
        >

            <div class="text-7xl mb-8">
                👨‍🏫
            </div>

            <h2 class="text-4xl font-bold text-[#062B66] mb-4">
                Kelola Dosen
            </h2>

            <p class="text-slate-500 text-lg leading-relaxed">
                Tambah dosen, atur NIDN,
                dan kelola mata kuliah yang diampu.
            </p>

            <div class="mt-8">

                <span class="bg-[#062B66] text-white px-5 py-3 rounded-xl inline-block">
                    Masuk Menu Dosen
                </span>

            </div>

        </a>

        <!-- MAHASISWA -->
        <a
            href="/admin/mahasiswa"
            class="bg-white rounded-3xl border border-slate-200 shadow-sm p-10 hover:scale-105 transition duration-300"
        >

            <div class="text-7xl mb-8">
                🎓
            </div>

            <h2 class="text-4xl font-bold text-[#062B66] mb-4">
                Kelola Mahasiswa
            </h2>

            <p class="text-slate-500 text-lg leading-relaxed">
                Tambah mahasiswa, hapus mahasiswa,
                dan kelola data akademik mahasiswa.
            </p>

            <div class="mt-8">

                <span class="bg-green-600 text-white px-5 py-3 rounded-xl inline-block">
                    Masuk Menu Mahasiswa
                </span>

            </div>

        </a>

    </div>

</div>