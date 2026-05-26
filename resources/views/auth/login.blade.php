<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign in — Shiv Edibles Procurement Portal</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Inter', ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            -webkit-font-smoothing: antialiased;
            background-color: #fafafa;
            background-image: radial-gradient(circle at top right, #f0fdf4 0%, #fafafa 100%);
        }
    </style>
</head>
<body class="text-zinc-800 min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-md bg-white border border-zinc-200/80 rounded-2xl p-8 shadow-xl shadow-zinc-200/50 relative overflow-hidden">
        <div class="absolute -top-24 -left-24 w-48 h-48 bg-emerald-500/5 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-24 -right-24 w-48 h-48 bg-emerald-700/5 rounded-full blur-3xl pointer-events-none"></div>

        <div class="flex flex-col items-center mb-8 relative">
            <div class="flex items-center gap-2 mb-3">
                <span class="w-2.5 h-6 bg-[#0d2818] rounded-full"></span>
                <h1 class="text-lg font-bold uppercase tracking-widest text-[#0d2818] leading-none">SHIV EDIBLES</h1>
            </div>
            <span class="text-xs text-zinc-500 font-medium">Procurement Management Dashboard</span>
        </div>

        @if ($errors->any())
            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg text-xs text-red-800">
                <div class="flex gap-2 items-start">
                    <i data-lucide="alert-circle" class="w-4 h-4 shrink-0 text-red-600 mt-0.5"></i>
                    <div>
                        <span class="font-semibold text-red-900">Authentication failed</span>
                        <ul class="list-disc pl-4 mt-1 space-y-1 text-red-700">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST" class="space-y-5 relative">
            @csrf

            <div class="space-y-1.5">
                <label for="username" class="text-[10px] font-bold uppercase tracking-wider text-zinc-400">Username</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-zinc-400">
                        <i data-lucide="user" class="w-4 h-4"></i>
                    </span>
                    <input type="text" name="username" id="username" value="{{ old('username', 'admin') }}" required autofocus autocomplete="username"
                        placeholder="admin"
                        class="w-full pl-9 pr-4 py-2.5 text-xs bg-zinc-50 border border-zinc-200 rounded-lg text-zinc-800 placeholder-zinc-400 focus:border-[#0d2818] focus:bg-white focus:outline-none transition">
                </div>
            </div>

            <div class="space-y-1.5">
                <label for="password" class="text-[10px] font-bold uppercase tracking-wider text-zinc-400">Password</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-zinc-400">
                        <i data-lucide="lock" class="w-4 h-4"></i>
                    </span>
                    <input type="password" name="password" id="password" required autocomplete="current-password"
                        placeholder="••••••••"
                        class="w-full pl-9 pr-10 py-2.5 text-xs bg-zinc-50 border border-zinc-200 rounded-lg text-zinc-800 placeholder-zinc-400 focus:border-[#0d2818] focus:bg-white focus:outline-none transition">
                    <button type="button" onclick="togglePasswordVisibility('password', 'password-toggle-icon')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-zinc-400 hover:text-zinc-650 cursor-pointer">
                        <i id="password-toggle-icon" data-lucide="eye" class="w-4 h-4"></i>
                    </button>
                </div>
            </div>

            <div class="flex items-center">
                <input type="checkbox" name="remember" id="remember"
                    class="h-3.5 w-3.5 text-[#0d2818] focus:ring-[#0d2818]/30 border-zinc-300 rounded bg-zinc-50">
                <label for="remember" class="ml-2 block text-[11px] text-zinc-500">Remember this device</label>
            </div>

            <button type="submit"
                class="w-full py-2.5 bg-[#0d2818] hover:bg-[#163a23] text-white font-bold text-xs rounded-lg transition duration-150 flex items-center justify-center gap-1.5 cursor-pointer shadow-md shadow-zinc-900/10">
                <i data-lucide="log-in" class="w-4 h-4"></i> Sign in
            </button>
        </form>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            lucide.createIcons();
        });

        function togglePasswordVisibility(inputId, iconId) {
            const passwordInput = document.getElementById(inputId);
            const toggleIcon = document.getElementById(iconId);
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.setAttribute('data-lucide', 'eye-off');
            } else {
                passwordInput.type = 'password';
                toggleIcon.setAttribute('data-lucide', 'eye');
            }
            lucide.createIcons();
        }
    </script>
</body>
</html>
