<script>

    import { toast } from '../stores/toastStore.js'

    export let user

    let oldPassword = ''
    let newPassword = ''
    let confirmPassword = ''

    let loading = false

    /*
    |--------------------------------------------------------------------------
    | UPDATE PASSWORD
    |--------------------------------------------------------------------------
    */

    async function updatePassword() {

        if (!oldPassword || !newPassword || !confirmPassword) {

            toast.warning('Semua field harus diisi!')
            return

        }

        if (newPassword !== confirmPassword) {

            toast.error('Password baru tidak cocok!')
            return

        }

        if (newPassword.length < 6) {

            toast.warning('Password minimal 6 karakter!')
            return

        }

        loading = true

        try {

            const response = await fetch('/akun/password', {

                method: 'POST',

                headers: {

                    'Content-Type': 'application/json',

                    'X-CSRF-TOKEN': document
                        .querySelector('meta[name="csrf-token"]')
                        .content,

                },

                body: JSON.stringify({

                    old_password: oldPassword,
                    password: newPassword,

                }),

            })

            const data = await response.json()

            if (response.ok) {

                toast.success(data.message || 'Password berhasil diubah! 🔒')

                oldPassword = ''
                newPassword = ''
                confirmPassword = ''

            } else {

                toast.error(data.message || 'Gagal mengubah password')

            }

        } catch (error) {

            toast.error('Terjadi kesalahan saat mengubah password')

        } finally {

            loading = false

        }

    }

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
                class="flex items-center gap-3 text-slate-600 hover:bg-slate-100 px-5 py-4 rounded-xl"
            >
                 Dashboard
            </a>

            <!-- SCAN -->
            <a
                href="/presensi"
                class="flex items-center gap-3 text-slate-600 hover:bg-slate-100 px-5 py-4 rounded-xl"
            >
                 Scan Presensi
            </a>

            <!-- RIWAYAT -->
            <a
                href="/riwayat-absensi"
                class="flex items-center gap-3 text-slate-600 hover:bg-slate-100 px-5 py-4 rounded-xl"
            >
                 Riwayat Absensi
            </a>

            <!-- PROFILE -->
            <a
                href="/akun"
                class="flex items-center gap-3 bg-slate-100 text-[#062B66] px-5 py-4 rounded-xl font-semibold"
            >
                 Profile
            </a>

        </nav>

        <button
            onclick={logout}
            class="w-full bg-red-50 text-red-600 hover:bg-red-100 px-5 py-3 rounded-xl font-semibold transition border border-red-200"
        >
             Logout
        </button>

    </aside>

    <!-- CONTENT -->
    <main class="flex-1 p-10 overflow-auto">

        <!-- TOPBAR -->
        <div class="flex justify-between items-center mb-10">

            <div>

                <h1 class="text-5xl font-bold text-[#062B66] mb-2">
                    Profile Saya
                </h1>

                <p class="text-slate-500 text-lg">
                    Kelola informasi profil dan keamanan akun
                </p>

            </div>

            <!-- BACK -->
            <a
                href="/mahasiswa/dashboard"
                class="bg-slate-600 hover:bg-slate-700 text-white px-6 py-3 rounded-xl font-semibold transition"
            >
                ← Kembali
            </a>

        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- PROFILE INFO -->
            <div class="lg:col-span-1">

                <div class="bg-white rounded-3xl shadow-sm p-8 border border-slate-200">

                    <!-- AVATAR -->
                    <div class="flex justify-center mb-6">

                        <div class="w-32 h-32 bg-linear-to-br from-[#062B66] to-[#0a3d8f] rounded-2xl flex items-center justify-center text-6xl text-white font-bold">

                            {user.name.charAt(0).toUpperCase()}

                        </div>

                    </div>

                    <!-- NAME -->
                    <h2 class="text-2xl font-bold text-[#062B66] text-center mb-6">

                        {user.name}

                    </h2>

                    <!-- DATA -->
                    <div class="space-y-4">

                        <div class="p-4 bg-slate-50 rounded-xl">

                            <p class="text-slate-500 text-sm font-semibold mb-1">
                                NIM
                            </p>

                            <p class="text-slate-800 font-bold text-lg">

                                {user.nim || '-'}

                            </p>

                        </div>

                        <div class="p-4 bg-slate-50 rounded-xl">

                            <p class="text-slate-500 text-sm font-semibold mb-1">
                                Email
                            </p>

                            <p class="text-slate-800 font-bold break-all">

                                {user.email}

                            </p>

                        </div>

                        <div class="p-4 bg-slate-50 rounded-xl">

                            <p class="text-slate-500 text-sm font-semibold mb-1">
                                Role
                            </p>

                            <p class="text-slate-800 font-bold capitalize">

                                {user.role}

                            </p>

                        </div>

                    </div>

                </div>

            </div>

            <!-- CHANGE PASSWORD -->
            <div class="lg:col-span-2">

                <div class="bg-white rounded-3xl shadow-sm p-8 border border-slate-200">

                    <h3 class="text-2xl font-bold text-[#062B66] mb-6">
                        Ubah Password
                    </h3>

                    <div class="space-y-5">

                        <!-- OLD PASSWORD -->
                        <div>

                            <label
                                for="oldPassword"
                                class="block text-slate-700 font-semibold mb-2"
                            >
                                Password Lama
                            </label>

                            <input
                                id="oldPassword"
                                bind:value={oldPassword}
                                type="password"
                                placeholder="Masukkan password lama"
                                class="w-full border border-slate-300 p-4 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#062B66]"
                            />

                        </div>

                        <!-- NEW PASSWORD -->
                        <div>

                            <label
                                for="newPassword"
                                class="block text-slate-700 font-semibold mb-2"
                            >
                                Password Baru
                            </label>

                            <input
                                id="newPassword"
                                bind:value={newPassword}
                                type="password"
                                placeholder="Masukkan password baru"
                                class="w-full border border-slate-300 p-4 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#062B66]"
                            />

                        </div>

                        <!-- CONFIRM -->
                        <div>

                            <label
                                for="confirmPassword"
                                class="block text-slate-700 font-semibold mb-2"
                            >
                                Konfirmasi Password Baru
                            </label>

                            <input
                                id="confirmPassword"
                                bind:value={confirmPassword}
                                type="password"
                                placeholder="Konfirmasi password baru"
                                class="w-full border border-slate-300 p-4 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#062B66]"
                            />

                        </div>

                        <!-- BUTTON -->
                        <button
                            onclick={updatePassword}
                            disabled={loading}
                            class="w-full bg-[#062B66] hover:bg-[#0a3d8f] disabled:bg-slate-400 text-white px-6 py-4 rounded-xl font-semibold transition mt-6"
                        >

                            {loading ? 'Sedang memproses...' : '🔒 Ubah Password'}

                        </button>

                    </div>

                    <!-- TIPS -->
                    <div class="mt-8 p-4 bg-blue-50 border border-blue-200 rounded-xl">

                        <p class="text-blue-700 text-sm">

                            <span class="font-semibold">
                                💡 Tips Keamanan:
                            </span>

                            <br/>

                            • Gunakan password yang kuat

                            <br/>

                            • Jangan bagikan password

                            <br/>

                            • Ubah password secara berkala

                        </p>

                    </div>

                </div>

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