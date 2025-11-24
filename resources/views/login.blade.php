<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Attendance System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* 🖋️ Add black text outline for better visibility */
        .outlined-text {
            color: white;
            font-weight: 700;
            text-shadow: 
                -1px -1px 2px black,
                 1px -1px 2px black,
                -1px  1px 2px black,
                 1px  1px 2px black;
        }
    </style>
</head>
<body 
    class="relative min-h-screen flex items-center justify-center p-4 bg-center bg-cover"
    style="background-image: url('{{ asset('images/Robinson_Tower_(c)_Tim_Griffith_(4).jpg') }}');"
>
    <!-- 🔴 Maroon transparent overlay -->
    <div class="absolute inset-0 bg-gradient-to-br from-red-800/70 to-red-950/70"></div>

    <!-- 🫧 Glass bubble box -->
    <div class="relative z-10 w-full max-w-md">
        <div class="backdrop-blur-xl bg-white/10 border border-white/20 rounded-3xl shadow-2xl overflow-hidden p-8 text-white font-semibold">
            
            <!-- Logo/Header -->
            <div class="text-center mb-8">
                <img src="{{ asset('images/utm_nobg.png') }}" alt="UTM Logo" class="w-36 h-28 mx-auto mb-4 drop-shadow-xl"> 
                <h1 class="text-2xl font-bold font-old ">Attendance Record</h1>
                <p class=" text-sl mt-2">Staff Portal</p>
            </div>

            @if(session('success'))
                <p id="logout-message"
                    class="bg-white border border-green-500 text-green-700 px-4 py-2 rounded-lg mt-4 text-center font-semibold shadow-md">
                    {{ session('success') }}
                </p>
            @endif


            <script>
               setTimeout(() => {
               const msg = document.getElementById('logout-message');
               if (msg) {
                 msg.style.transition = "opacity 0.5s";
                 msg.style.opacity = "0";
                 setTimeout(() => msg.remove(), 500);
                }
              }, 10000); // 10 seconds
            </script>

            <!-- Laravel Real Login Form -->
            <form method="POST" action="{{ url('/login') }}" class="space-y-6">
                @csrf
                <!-- Email -->
                <div>
                    <label class="block text-sm font-bold mb-2">
                        <i class="fas fa-envelope mr-1"></i> Email
                    </label>
                    <input 
                        type="email" 
                        name="email"
                        value="{{ old('email') }}"
                        required 
                        class="w-full px-4 py-2 bg-white/40 text-black font-semibold placeholder-gray-700 border border-white/50 rounded-lg focus:ring-2 focus:ring-blue-400 focus:border-transparent backdrop-blur-md transition"
                        placeholder="Enter your email"
                    >


                    @error('email')
                        <p class="text-red-300 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label class="block text-sm font-bold mb-2">
                        <i class="fas fa-lock mr-1"></i> Password
                    </label>
                    <div class="relative">
                        <input 
                            id="password"
                            type="password" 
                            name="password"
                            required 
                            class="w-full px-4 py-2 bg-white/40 text-black font-semibold placeholder-gray-700 border border-white/50 rounded-lg focus:ring-2 focus:ring-blue-400 focus:border-transparent backdrop-blur-md transition"
                            placeholder="Enter your password"
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
                        <p class="text-red-300 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Remember me -->
                <div class="flex items-center justify-between">
                    <label class="flex items-center text-sm ">
                        <input type="checkbox" name="remember" class="w-4 h-4 text-red-500 border-white/40 bg-white/10 rounded focus:ring-red-400">
                        <span class="ml-2">Remember me</span>
                    </label>
                </div>

                <!-- Login Button -->
                <button 
                    type="submit"
                    class="w-full bg-red-700/80 hover:bg-red-800/90 text-white py-3 rounded-lg font-bold shadow-lg hover:shadow-2xl transition duration-200 backdrop-blur-md "
                >
                    <i class="fas fa-sign-in-alt mr-2"></i> Login
                </button>

                <!-- Error & Success messages -->
                @if(session('error'))
                    <p class="text-red-300 text-sm text-center mt-4">{{ session('error') }}</p>
                @endif
            </form>
        </div>

        <div class="text-center mt-6 text-gray-200 text-sm font-medium drop-shadow outlined-text">
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
        document.addEventListener('DOMContentLoaded', function() {
        const successMessage = document.getElementById('successMessage');
        if (successMessage) {
            setTimeout(() => {
                successMessage.style.transition = 'opacity 0.5s ease';
                successMessage.style.opacity = '0';
                setTimeout(() => successMessage.remove(), 500); // remove after fade-out
            }, 10000); // 10 seconds
        }
        });
    </script>

</body>
</html>
