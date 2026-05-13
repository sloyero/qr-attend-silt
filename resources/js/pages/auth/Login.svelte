<script>

    let email = ''
    let password = ''

    async function login() {

        await fetch('/login', {

            method: 'POST',

            headers: {

                'Content-Type': 'application/json',

                'X-CSRF-TOKEN': document
                    .querySelector('meta[name="csrf-token"]')
                    .content,

            },

            body: JSON.stringify({

                email,
                password,

            }),

        })

        window.location.href = '/dashboard'

    }

</script>

<div class="min-h-screen bg-slate-900 flex items-center justify-center">

    <form
        on:submit|preventDefault={login}
        class="bg-white p-8 rounded-xl w-96"
    >

        <h1 class="text-3xl font-bold mb-5">
            Login
        </h1>

        <input
            bind:value={email}
            type="email"
            placeholder="Email"
            class="w-full border p-3 mb-4 rounded-lg"
        />

        <input
            bind:value={password}
            type="password"
            placeholder="Password"
            class="w-full border p-3 mb-4 rounded-lg"
        />

        <button
            class="w-full bg-blue-600 text-white p-3 rounded-lg"
        >
            Login
        </button>

    </form>

</div>