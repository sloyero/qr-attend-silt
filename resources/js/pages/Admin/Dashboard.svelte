<script>
    import { Html5Qrcode } from 'html5-qrcode'
    import { onDestroy } from 'svelte'

    export let user

    let scanning = false
    let scanner = null
    let scanResult = null
    let scanError = null
    let scanLoading = false

    async function startScanner() {

        scanning = true
        scanResult = null
        scanError = null

        await new Promise(r => setTimeout(r, 100))

        scanner = new Html5Qrcode('qr-reader')

        try {

            await scanner.start(
                { facingMode: 'environment' },
                {
                    fps: 10,
                    qrbox: { width: 250, height: 250 },
                },

                async (decodedText) => {

                    await stopScanner()
                    await kirimAbsensi(decodedText)

                },

                () => {}

            )

        } catch (err) {

            scanError = 'Tidak dapat mengakses kamera.'

            scanning = false

        }

    }

    async function stopScanner() {

        if (scanner) {

            try {

                await scanner.stop()

            } catch (e) {}

            scanner = null

        }

        scanning = false

    }

    async function kirimAbsensi(token) {

        scanLoading = true
        scanResult = null
        scanError = null

        try {

            const res = await fetch('/absensi/scan', {

                method: 'POST',

                headers: {

                    'Content-Type': 'application/json',

                    'X-CSRF-TOKEN': document
                        .querySelector('meta[name="csrf-token"]')
                        .content,

                },

                body: JSON.stringify({ token }),

            })

            const data = await res.json()

            if (data.success) {

                scanResult = {

                    message: data.message,
                    status: data.status,

                }

            } else {

                scanError = data.message

            }

        } catch (err) {

            scanError = 'Terjadi kesalahan jaringan.'

        }

        scanLoading = false

    }

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

    onDestroy(() => {

        if (scanner) {

            try {

                scanner.stop()

            } catch(e) {}

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

            <!-- PROFILE -->
            <a
                href="/akun"
                class="flex items-center gap-3 text-slate-600 hover:bg-slate-100 px-5 py-4 rounded-xl"
            >
                👤 Profile
            </a>

            <!-- RIWAYAT -->
            <a
                href="/riwayat-absensi"
                class="flex items-center gap-3 text-slate-600 hover:bg-slate-100 px-5 py-4 rounded-xl"
            >
                📄 Riwayat Kehadiran
            </a>

        </nav>

    </aside>

    <!-- CONTENT -->
    <main class="flex-1 p-10 overflow-auto">

        <!-- TOPBAR -->
        <div class="flex justify-between items-center mb-10">

            <div>

                <h1 class="text-5xl font-bold text-[#062B66] mb-2">
                    Dashboard
                </h1>

                <p class="text-slate-500 text-xl">
                    Selamat datang, {user.name}
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
                    onclick={logout}
                    class="bg-white shadow px-5 py-3 rounded-xl border border-slate-200 hover:bg-red-50"
                >
                    Logout
                </button>

            </div>

        </div>

        <!-- SCAN QR -->
        <div class="bg-white rounded-3xl shadow-sm p-10 mb-8 border border-slate-200">

            <div class="text-center max-w-lg mx-auto">

                <div class="text-6xl mb-6">
                    📷
                </div>

                <h2 class="text-3xl font-bold text-[#062B66] mb-3">
                    Scan QR Code Presensi
                </h2>

                <p class="text-slate-500 text-lg mb-8">
                    Scan QR dari dosen untuk absensi
                </p>

                <!-- RESULT -->
                {#if scanResult}

                    <div class="mb-6 p-6 rounded-2xl bg-green-50 border border-green-200">

                        <div class="text-4xl mb-3">
                            ✅
                        </div>

                        <p class="text-xl font-bold text-green-700">
                            {scanResult.message}
                        </p>

                    </div>

                {/if}

                {#if scanError}

                    <div class="mb-6 p-6 rounded-2xl bg-red-50 border border-red-200">

                        <div class="text-4xl mb-3">
                            ❌
                        </div>

                        <p class="text-lg font-semibold text-red-700">
                            {scanError}
                        </p>

                    </div>

                {/if}

                <!-- QR -->
                {#if scanning}

                    <div class="mb-6">

                        <div
                            id="qr-reader"
                            class="rounded-2xl overflow-hidden mx-auto"
                            style="max-width: 400px;"
                        ></div>

                    </div>

                    <button
                        onclick={stopScanner}
                        class="bg-red-500 hover:bg-red-600 text-white px-8 py-4 rounded-xl"
                    >
                        Batal
                    </button>

                {:else}

                    <button
                        onclick={startScanner}
                        class="bg-[#062B66] hover:bg-[#0a3d8f] text-white px-10 py-5 rounded-2xl font-bold text-xl"
                    >
                        📷 Scan QR Code
                    </button>

                {/if}

            </div>

        </div>

    </main>

</div>