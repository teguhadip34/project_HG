
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - EcoScan AI</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen bg-gradient-to-br from-green-50 via-white to-emerald-100 flex items-center justify-center p-4">

    <div class="w-full max-w-md bg-white rounded-3xl shadow-xl p-8">

        <!-- Logo dan Header -->
        <div class="text-center mb-8">
            <div class="mx-auto w-16 h-16 bg-green-100 rounded-2xl flex items-center justify-center text-3xl mb-4">
                ♻️
            </div>

            <h1 class="text-3xl font-extrabold text-green-700">
                EcoScan AI
            </h1>

            <p class="text-gray-500 mt-2">
                Selamat datang kembali! 🌱
            </p>
        </div>

        <!-- Notifikasi Berhasil -->
        @if (session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 rounded-xl p-3 mb-5 text-sm">
                {{ session('success') }}
            </div>
        @endif

        <!-- Notifikasi Error -->
        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-600 rounded-xl p-3 mb-5 text-sm">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <!-- Form Login -->
        <form action="{{ route('login') }}" method="POST" class="space-y-5">
            @csrf

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Email
                </label>

                <input
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autofocus
                    placeholder="nama@email.com"
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-green-500 focus:ring-2 focus:ring-green-100 outline-none transition"
                >
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Password
                </label>

                <input
                    type="password"
                    name="password"
                    required
                    placeholder="Masukkan password"
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-green-500 focus:ring-2 focus:ring-green-100 outline-none transition"
                >
            </div>

            <div class="flex items-center gap-2">
                <input
                    type="checkbox"
                    name="remember"
                    value="1"
                    class="w-4 h-4 accent-green-600"
                >

                <label class="text-sm text-gray-600">
                    Ingat saya
                </label>
            </div>

            <button
                type="submit"
                class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 rounded-xl shadow-lg shadow-green-200 transition duration-200">
                Login Sekarang
            </button>
        </form>

        <!-- Register -->
        <p class="text-center text-sm text-gray-500 mt-6">
            Belum punya akun?

            <a href="{{ route('register') }}" class="text-green-600 font-bold hover:underline">
                Daftar
            </a>
        </p>

        <!-- Guest -->
        <form method="POST" action="{{ route('guest') }}" class="mt-4">
            @csrf

            <button
                type="submit"
                class="w-full border border-green-200 text-green-700 hover:bg-green-50 font-semibold py-3 rounded-xl transition duration-200">
                Masuk sebagai Guest
            </button>
        </form>

        <!-- Footer -->
        <div class="mt-8 pt-5 border-t border-gray-100 text-center">
            <p class="text-xs text-gray-400">
                © {{ date('Y') }} EcoScan AI. Peduli Lingkungan, Mulai dari Kita.
            </p>
        </div>

    </div>

</body>
</html>
