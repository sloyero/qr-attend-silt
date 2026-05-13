<script>

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

<div class="min-h-screen bg-slate-900 text-white p-10">

    <div class="flex justify-between items-center mb-10">

        <h1 class="text-4xl font-bold">
            Dashboard Admin
        </h1>

        <button
            on:click={logout}
            class="bg-red-600 px-4 py-2 rounded-lg"
        >
            Logout
        </button>

    </div>

</div>