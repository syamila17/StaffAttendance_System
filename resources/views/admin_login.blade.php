<!DOCTYPE html>
<html lang="{{ app()->getLocale() ?? 'en' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('auth.login') }} - {{ __('auth.attendance_system') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: 
                linear-gradient(to bottom right, rgba(128, 0, 0, 0.6), rgba(255, 69, 0, 0.6)),
                url("{{ asset('images/Robinson_Tower_(c)_Tim_Griffith_(4).jpg') }}") no-repeat center center fixed;
            background-size: cover;
        }
    </style>
</head>

<body class="min-h-screen flex items-center justify-center p-4">
    <!-- Staff Login Link (Top Right) -->
    <div class="absolute top-6 right-6 z-20">
        <a href="{{ route('login') }}" class="bg-yellow-400/90 hover:bg-yellow-300 text-yellow-900 px-4 py-2 rounded-lg font-bold transition flex items-center gap-2 shadow-lg">
            <i class="fas fa-user"></i>Staff Login
        </a>
    </div>

    <div class="w-full max-w-md">
        <div class="bg-white/25 backdrop-blur-md rounded-2xl shadow-2xl overflow-hidden border border-white/40">
            <div class="p-8">
                <!-- Logo/Header -->
                <div class="text-center mb-8">
                    <img src="{{ asset('images/UTMLOGO.png') }}" alt="UTM Logo" class="w-38 h-28 mx-auto mb-4">
                    <h1 class="text-2xl text-white drop-shadow-md">{{ __('auth.admin_management') }}</h1>
                    <!-- <p class="text-white text-sm mt-2 font-semibold drop-shadow-md">{{ __('auth.admin_portal') }}</p> -->
                     <!-- ADMIN PORTAL Badge -->
                    <div class="mt-3 inline-block">
                        <span class="bg-yellow-400 text-yellow-900 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider">
                            <i class="fas fa-lock mr-1"></i> ADMIN PORTAL
                        </span>
                    </div>
                </div>

                <!-- Language Switcher -->
                <div class="flex gap-2 mb-6 justify-center">
                    <a href="{{ route('admin.login', ['lang' => 'en']) }}" class="px-3 py-1 rounded text-sm font-semibold {{ app()->getLocale() == 'en' ? 'bg-white text-orange-700' : 'bg-white/30 text-white hover:bg-white/40' }} transition">
                        ENG
                    </a>
                    <a href="{{ route('admin.login', ['lang' => 'ms']) }}" class="px-3 py-1 rounded text-sm font-semibold {{ app()->getLocale() == 'ms' ? 'bg-white text-orange-700' : 'bg-white/30 text-white hover:bg-white/40' }} transition">
                        BM
                    </a>
                </div>
                @if (session('success'))
                    <div id="successMessage" 
                        class="bg-green-100 border border-green-400 text-green-700 px-2 py-2 rounded relative mt-4 text-center font-semibold shadow-md"
                        role="alert">
                        <strong class="font-bold">{{ __('auth.success_login') }}</strong>
                        <span class="block sm:inline">{{ session('success') }}</span>
                    </div>

                    <script>
                // Auto-hide message after 10 seconds (10,000 ms)
                        setTimeout(() => {
                        const msg = document.getElementById('successMessage');
                        if (msg) {
                            msg.style.transition = 'opacity 0.5s ease';
                            msg.style.opacity = '0';
                            setTimeout(() => msg.remove(), 500); // remove after fade-out 
                            }
                        }, 10000);
                    </script>
                @endif

                @if (session('error'))
                    <div id="errorMessage" 
                        class="bg-red-100 border border-red-400 text-red-700 px-2 py-2 rounded relative mt-4 text-center font-semibold shadow-md"
                        role="alert">
                        <strong class="font-bold">{{ __('auth.error') }}</strong>
                        <span class="block sm:inline">{{ session('error') }}</span>
                    </div>

                    <script>
                // Auto-hide message after 10 seconds (10,000 ms)
                        setTimeout(() => {
                        const msg = document.getElementById('errorMessage');
                        if (msg) {
                            msg.style.transition = 'opacity 0.5s ease';
                            msg.style.opacity = '0';
                            setTimeout(() => msg.remove(), 500); // remove after fade-out 
                            }
                        }, 10000);
                    </script>
                @endif

                <!-- Laravel Real Login Form -->
                <form method="POST" action="{{ url('/admin_login') }}" class="space-y-6">
                    @csrf

                    <!-- Email -->
                    <div>
                        <label class="block text-white font-semibold mb-2 drop-shadow-md">
                            <i class="fas fa-envelope mr-1"></i> {{ __('auth.email') }}
                        </label>
                        <input 
                            type="email" 
                            name="email"
                            value="{{ old('email') }}"
                            required 
                            class="w-full px-4 py-2 border border-white/60 rounded-lg bg-white/60 text-black font-semibold placeholder-gray-700 focus:ring-2 focus:ring-orange-400 focus:border-transparent transition"
                            placeholder="{{ __('auth.email_placeholder') }}"
                        >
                        @error('email')
                            <p class="text-red-200 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div>
                        <label class="block text-white font-semibold mb-2 drop-shadow-md">
                            <i class="fas fa-lock mr-1"></i> {{ __('auth.password') }}
                        </label>
                        <div class="relative">
                            <input 
                                id="password"
                                type="password" 
                                name="password"
                                required 
                                class="w-full px-4 py-2 border border-white/60 rounded-lg bg-white/60 text-black font-semibold placeholder-gray-700 focus:ring-2 focus:ring-orange-400 focus:border-transparent transition"
                                placeholder="{{ __('auth.password_placeholder') }}"
                            >
                            <button 
                                type="button" 
                                onclick="togglePassword()"
                                class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-700 hover:text-black transition"
                            >
                                <i class="fas fa-eye" id="toggleIcon"></i>
                            </button>
                        </div>

                        
                        @error('password')
                            <p class="text-red-200 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Remember me -->
                    <div class="flex items-center justify-between">
                        <label class="flex items-center">
                            <input type="checkbox" name="remember" class="w-4 h-4 text-orange-500 border-gray-300 rounded focus:ring-orange-500">
                            <span class="ml-2 text-sm text-white font-semibold drop-shadow-md">{{ __('auth.remember_me') }}</span>
                        </label>
                    </div>

                    <!-- Login Button -->
                    <button 
                        type="submit"
                        class="w-full bg-gradient-to-r from-[#8B0000] to-[#FF4500] hover:from-[#A52A2A] hover:to-[#FF6347] text-white py-3 rounded-lg transition duration-300 font-semibold shadow-lg hover:shadow-xl"
                    >
                        <i class="fas fa-sign-in-alt mr-2"></i> {{ __('auth.login') }}
                    </button>
                </form>

            </div>
        </div>

        <div class="text-center mt-6 text-white text-sm font-semibold drop-shadow-md">
            <p>&copy; 2025 Attendance Management System. All rights reserved.</p>
        </div>
    </div>
    
    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');
    
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            toggleIcon.classList.remove('fa-eye');
            toggleIcon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            toggleIcon.classList.remove('fa-eye-slash');
            toggleIcon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>
