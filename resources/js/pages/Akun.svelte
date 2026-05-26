<script>

    export let user

    let password = ''

    async function updatePassword() {

        await fetch('/akun/password', {

            method: 'POST',

            headers: {

                'Content-Type': 'application/json',

                'X-CSRF-TOKEN': document
                    .querySelector('meta[name="csrf-token"]')
                    .content,

            },

            body: JSON.stringify({

                password,

            }),

        })

        alert('Password berhasil diubah')

    }

</script>

<div class="min-h-screen bg-slate-100 p-10">

    <div class="max-w-2xl mx-auto bg-white rounded-3xl shadow-sm border border-slate-200 p-10">

        <h1 class="text-5xl font-bold text-[#062B66] mb-10">
            Profile Mahasiswa
        </h1>

        <!-- NIM -->
        <div class="mb-6">

            <label class="block text-slate-500 mb-2">
                NIM
            </label>

            <input
                value={user.nim}
                disabled
                class="w-full border border-slate-300 rounded-xl p-4 bg-slate-100"
            />

        </div>

        <!-- NAMA -->
        <div class="mb-6">

            <label class="block text-slate-500 mb-2">
                Nama
            </label>

            <input
                value={user.name}
                disabled
                class="w-full border border-slate-300 rounded-xl p-4 bg-slate-100"
            />

        </div>

        <!-- PASSWORD -->
        <div class="mb-8">

            <label class="block text-slate-500 mb-2">
                Password Baru
            </label>

            <input
                bind:value={password}
                type="password"
                placeholder="Masukkan password baru"
                class="w-full border border-slate-300 rounded-xl p-4"
            />

        </div>

        <!-- BUTTON -->
        <button
            on:click={updatePassword}
            class="bg-[#062B66] text-white px-6 py-4 rounded-xl"
        >
            Simpan Password
        </button>

    </div>

</div>