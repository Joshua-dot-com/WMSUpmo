<?php
$token = $_GET['token'] ?? '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Your Password</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="font-[Poppins] antialiased text-gray-800 bg-red-50">
    <!-- Animated Background Elements -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <!-- Animated circles -->
        <div class="absolute top-20 left-20 w-64 h-64 bg-red-100 rounded-full opacity-60 animate-float"></div>
        <div class="absolute bottom-20 right-20 w-80 h-80 bg-red-100 rounded-full opacity-60 animate-float animation-delay-2000"></div>
        <div class="absolute top-1/2 right-1/4 w-40 h-40 bg-red-100 rounded-full opacity-60 animate-float animation-delay-4000"></div>
        
        <!-- Animated particles -->
        <div class="particles absolute inset-0 w-full h-full overflow-hidden"></div>
    </div>

    <div class="min-h-screen flex flex-col items-center justify-center px-4 relative z-10">
        <!-- Logo and branding -->
        <div class="text-center mb-8 animate-fade-in-down">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-white shadow-xl mb-4 relative overflow-hidden">
                <div class="absolute inset-0 bg-red-100"></div>
                <div class="relative z-10 transform transition-transform duration-700 hover:rotate-12">
                    <i class="fas fa-lock text-4xl text-red-600 animate-bounce-subtle"></i>
                </div>
            </div>
            <h1 class="text-3xl font-bold text-red-600 animate-fade-in">Secure Password Reset</h1>
            <p class="text-gray-600 mt-2 animate-fade-in">Create a new password for your account</p>
        </div>

        <!-- Card container -->
        <div class="w-full max-w-md transform transition-all duration-500 hover:scale-[1.02] animate-fade-in-up">
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-red-100">
                <!-- Card header -->
                <div class="bg-red-600 px-6 py-5 relative">
                    <div class="absolute inset-0 bg-pattern opacity-10"></div>
                    <h2 class="text-xl font-semibold text-white flex items-center">
                        <i class="fas fa-key mr-2 animate-wiggle"></i>
                        Reset Your Password
                    </h2>
                    <p class="text-red-100 text-sm mt-1">Please choose a strong, unique password</p>
                </div>

                <!-- Card body -->
                <div class="p-8">
                    <form method="POST" action="admin-reset-password-process.php" class="space-y-6">
                        <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

                        <!-- Password field -->
                        <div class="space-y-2 animate-fade-in" style="--delay: 100ms;">
                            <label for="password" class="block text-sm font-medium text-gray-700">New Password</label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400 group-focus-within:text-red-600 transition-colors">
                                    <i class="fas fa-lock"></i>
                                </div>
                                <input type="password" id="password" name="password" required 
                                       class="block w-full pl-10 pr-10 py-3.5 text-gray-700 bg-gray-50 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-600 focus:border-red-600 transition-all"
                                       placeholder="Enter your new password">
                                <button type="button" id="togglePassword" class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-red-600 transition-colors">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <div class="absolute bottom-0 left-0 h-0.5 w-0 bg-red-600 group-focus-within:w-full transition-all duration-300"></div>
                            </div>
                            
                            <!-- Password strength meter -->
                            <div class="mt-3">
                                <div class="w-full bg-gray-200 rounded-full h-1.5 overflow-hidden">
                                    <div id="password-strength" class="bg-red-600 h-1.5 rounded-full transition-all duration-500 ease-out" style="width: 0%"></div>
                                </div>
                                <p id="password-strength-text" class="text-xs text-gray-500 mt-1">Password strength: Too weak</p>
                            </div>
                            
                            <ul class="text-xs text-gray-500 space-y-1.5 mt-3 grid grid-cols-2 gap-x-2 gap-y-1.5">
                                <li id="length-check" class="flex items-center transform transition-all duration-300 hover:translate-x-1">
                                    <i class="fas fa-times-circle mr-1.5 text-red-500"></i>
                                    <span>At least 8 characters</span>
                                </li>
                                <li id="uppercase-check" class="flex items-center transform transition-all duration-300 hover:translate-x-1">
                                    <i class="fas fa-times-circle mr-1.5 text-red-500"></i>
                                    <span>One uppercase letter</span>
                                </li>
                                <li id="number-check" class="flex items-center transform transition-all duration-300 hover:translate-x-1">
                                    <i class="fas fa-times-circle mr-1.5 text-red-500"></i>
                                    <span>One number</span>
                                </li>
                                <li id="special-check" class="flex items-center transform transition-all duration-300 hover:translate-x-1">
                                    <i class="fas fa-times-circle mr-1.5 text-red-500"></i>
                                    <span>One special character</span>
                                </li>
                            </ul>
                        </div>

                        <!-- Confirm password field -->
                        <div class="space-y-2 animate-fade-in" style="--delay: 200ms;">
                            <label for="confirm_password" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400 group-focus-within:text-red-600 transition-colors">
                                    <i class="fas fa-shield-alt"></i>
                                </div>
                                <input type="password" id="confirm_password" name="confirm_password" required 
                                       class="block w-full pl-10 pr-10 py-3.5 text-gray-700 bg-gray-50 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-600 focus:border-red-600 transition-all"
                                       placeholder="Confirm your new password">
                                <button type="button" id="toggleConfirmPassword" class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-red-600 transition-colors">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <div class="absolute bottom-0 left-0 h-0.5 w-0 bg-red-600 group-focus-within:w-full transition-all duration-300"></div>
                            </div>
                            <div id="password-match-container" class="mt-1 hidden">
                                <p id="password-match" class="text-xs flex items-center">
                                    <i id="match-icon" class="fas fa-check-circle mr-1.5 text-green-500"></i>
                                    <span id="match-text">Passwords match</span>
                                </p>
                            </div>
                        </div>

                        <!-- Submit button -->
                        <div class="pt-2 animate-fade-in" style="--delay: 300ms;">
                            <button type="submit" id="submit-btn" disabled
                                    class="w-full py-3.5 px-4 bg-red-600 text-white font-medium rounded-xl shadow-lg shadow-red-200 transition-all duration-300 transform hover:translate-y-[-2px] active:translate-y-[1px] disabled:opacity-60 disabled:cursor-not-allowed disabled:hover:translate-y-0 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-600 focus:ring-offset-2">
                                <span class="relative flex items-center justify-center">
                                    <i class="fas fa-check-circle mr-2"></i>
                                    Reset Password
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Additional info -->
            <div class="text-center mt-6 animate-fade-in" style="--delay: 400ms;">
                <p class="text-sm text-gray-600">
                    Remember your password? 
                    <a href="login.php" class="font-medium text-red-600 hover:text-red-700 transition-colors relative inline-block group">
                        Back to login
                        <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-red-600 group-hover:w-full transition-all duration-300"></span>
                    </a>
                </p>
            </div>
            
            <!-- Security note -->
            <div class="text-center mt-4 animate-fade-in" style="--delay: 500ms;">
                <p class="text-xs text-gray-500 flex items-center justify-center">
                    <i class="fas fa-shield-alt text-red-500 mr-1.5"></i>
                    Your password is securely encrypted and never stored in plain text
                </p>
            </div>
        </div>
    </div>

    <script>
        // Toggle password visibility
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const icon = this.querySelector('i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
                this.classList.add('text-red-600');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
                this.classList.remove('text-red-600');
            }
        });

        document.getElementById('toggleConfirmPassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('confirm_password');
            const icon = this.querySelector('i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
                this.classList.add('text-red-600');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
                this.classList.remove('text-red-600');
            }
        });

        // Password strength checker
        const passwordInput = document.getElementById('password');
        const confirmPasswordInput = document.getElementById('confirm_password');
        const strengthBar = document.getElementById('password-strength');
        const strengthText = document.getElementById('password-strength-text');
        const submitBtn = document.getElementById('submit-btn');
        const passwordMatchContainer = document.getElementById('password-match-container');
        const passwordMatch = document.getElementById('password-match');
        const matchIcon = document.getElementById('match-icon');
        const matchText = document.getElementById('match-text');
        
        // Password requirement checks
        const lengthCheck = document.getElementById('length-check');
        const uppercaseCheck = document.getElementById('uppercase-check');
        const numberCheck = document.getElementById('number-check');
        const specialCheck = document.getElementById('special-check');

        passwordInput.addEventListener('input', function() {
            const password = this.value;
            let strength = 0;
            let status = '';
            
            // Update requirement checks with animations
            if (password.length >= 8) {
                lengthCheck.innerHTML = '<i class="fas fa-check-circle mr-1.5 text-green-500 animate-bounce-once"></i><span>At least 8 characters</span>';
                strength += 25;
            } else {
                lengthCheck.innerHTML = '<i class="fas fa-times-circle mr-1.5 text-red-500"></i><span>At least 8 characters</span>';
            }
            
            if (password.match(/[A-Z]/)) {
                uppercaseCheck.innerHTML = '<i class="fas fa-check-circle mr-1.5 text-green-500 animate-bounce-once"></i><span>One uppercase letter</span>';
                strength += 25;
            } else {
                uppercaseCheck.innerHTML = '<i class="fas fa-times-circle mr-1.5 text-red-500"></i><span>One uppercase letter</span>';
            }
            
            if (password.match(/[0-9]/)) {
                numberCheck.innerHTML = '<i class="fas fa-check-circle mr-1.5 text-green-500 animate-bounce-once"></i><span>One number</span>';
                strength += 25;
            } else {
                numberCheck.innerHTML = '<i class="fas fa-times-circle mr-1.5 text-red-500"></i><span>One number</span>';
            }
            
            if (password.match(/[^A-Za-z0-9]/)) {
                specialCheck.innerHTML = '<i class="fas fa-check-circle mr-1.5 text-green-500 animate-bounce-once"></i><span>One special character</span>';
                strength += 25;
            } else {
                specialCheck.innerHTML = '<i class="fas fa-times-circle mr-1.5 text-red-500"></i><span>One special character</span>';
            }
            
            // Update strength bar with animation
            strengthBar.style.width = strength + '%';
            
            if (strength <= 25) {
                strengthBar.className = 'bg-red-500 h-1.5 rounded-full transition-all duration-500 ease-out';
                status = 'Too weak';
            } else if (strength <= 50) {
                strengthBar.className = 'bg-orange-500 h-1.5 rounded-full transition-all duration-500 ease-out';
                status = 'Weak';
            } else if (strength <= 75) {
                strengthBar.className = 'bg-yellow-500 h-1.5 rounded-full transition-all duration-500 ease-out';
                status = 'Good';
            } else {
                strengthBar.className = 'bg-green-500 h-1.5 rounded-full transition-all duration-500 ease-out';
                status = 'Strong';
            }
            
            strengthText.textContent = 'Password strength: ' + status;
            
            // Check if passwords match
            checkPasswordMatch();
            
            // Enable/disable submit button
            updateSubmitButton();
        });
        
        confirmPasswordInput.addEventListener('input', checkPasswordMatch);
        
        function checkPasswordMatch() {
            const password = passwordInput.value;
            const confirmPassword = confirmPasswordInput.value;
            
            if (confirmPassword) {
                passwordMatchContainer.classList.remove('hidden');
                
                if (password === confirmPassword) {
                    matchText.textContent = 'Passwords match';
                    matchIcon.className = 'fas fa-check-circle mr-1.5 text-green-500 animate-bounce-once';
                    passwordMatch.className = 'text-xs text-green-500 flex items-center';
                    confirmPasswordInput.classList.remove('border-red-500');
                    confirmPasswordInput.classList.add('border-green-500');
                } else {
                    matchText.textContent = 'Passwords do not match';
                    matchIcon.className = 'fas fa-times-circle mr-1.5 text-red-500';
                    passwordMatch.className = 'text-xs text-red-500 flex items-center';
                    confirmPasswordInput.classList.remove('border-green-500');
                    confirmPasswordInput.classList.add('border-red-500');
                }
            } else {
                passwordMatchContainer.classList.add('hidden');
                confirmPasswordInput.classList.remove('border-green-500', 'border-red-500');
            }
            
            // Enable/disable submit button
            updateSubmitButton();
        }
        
        function updateSubmitButton() {
            const password = passwordInput.value;
            const confirmPassword = confirmPasswordInput.value;
            let strength = 0;
            
            if (password.length >= 8) strength += 25;
            if (password.match(/[A-Z]/)) strength += 25;
            if (password.match(/[0-9]/)) strength += 25;
            if (password.match(/[^A-Za-z0-9]/)) strength += 25;
            
            // Enable button only if password is strong enough and passwords match
            if (strength >= 75 && password === confirmPassword && confirmPassword) {
                submitBtn.disabled = false;
                submitBtn.classList.add('animate-pulse-subtle');
            } else {
                submitBtn.disabled = true;
                submitBtn.classList.remove('animate-pulse-subtle');
            }
        }

        // Create animated particles
        document.addEventListener('DOMContentLoaded', function() {
            const particlesContainer = document.querySelector('.particles');
            for (let i = 0; i < 30; i++) {
                createParticle(particlesContainer);
            }
        });

        function createParticle(container) {
            const particle = document.createElement('div');
            particle.className = 'particle';
            
            // Random position
            const posX = Math.random() * 100;
            const posY = Math.random() * 100;
            
            // Random size
            const size = Math.random() * 10 + 3;
            
            // Random opacity
            const opacity = Math.random() * 0.3 + 0.1;
            
            // Random animation duration
            const duration = Math.random() * 20 + 10;
            
            // Random animation delay
            const delay = Math.random() * 5;
            
            // Set styles
            particle.style.left = `${posX}%`;
            particle.style.top = `${posY}%`;
            particle.style.width = `${size}px`;
            particle.style.height = `${size}px`;
            particle.style.opacity = opacity;
            particle.style.animationDuration = `${duration}s`;
            particle.style.animationDelay = `${delay}s`;
            particle.style.backgroundColor = '#ef4444';
            
            container.appendChild(particle);
        }
    </script>

    <style>
        /* Base animations */
        @keyframes float {
            0% {
                transform: translate(0px, 0px) scale(1);
            }
            33% {
                transform: translate(30px, -50px) scale(1.1);
            }
            66% {
                transform: translate(-20px, 20px) scale(0.9);
            }
            100% {
                transform: translate(0px, 0px) scale(1);
            }
        }
        
        @keyframes pulse-subtle {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.02); }
        }
        
        @keyframes bounce-subtle {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-5px); }
        }
        
        @keyframes bounce-once {
            0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
            40% { transform: translateY(-5px); }
            60% { transform: translateY(-2px); }
        }
        
        @keyframes wiggle {
            0%, 100% { transform: rotate(0); }
            25% { transform: rotate(10deg); }
            75% { transform: rotate(-10deg); }
        }
        
        @keyframes fade-in {
            0% { opacity: 0; }
            100% { opacity: 1; }
        }
        
        @keyframes fade-in-down {
            0% { opacity: 0; transform: translateY(-20px); }
            100% { opacity: 1; transform: translateY(0); }
        }
        
        @keyframes fade-in-up {
            0% { opacity: 0; transform: translateY(20px); }
            100% { opacity: 1; transform: translateY(0); }
        }
        
        @keyframes float-particle {
            0% { transform: translateY(0) rotate(0deg); }
            100% { transform: translateY(-100vh) rotate(360deg); }
        }
        
        /* Apply animations */
        .animate-float {
            animation: float 15s ease-in-out infinite;
        }
        
        .animate-pulse-subtle {
            animation: pulse-subtle 2s ease-in-out infinite;
        }
        
        .animate-bounce-subtle {
            animation: bounce-subtle 2s ease-in-out infinite;
        }
        
        .animate-bounce-once {
            animation: bounce-once 1s ease-in-out;
        }
        
        .animate-wiggle {
            animation: wiggle 2s ease-in-out infinite;
        }
        
        .animate-fade-in {
            animation: fade-in 0.6s ease-out forwards;
            opacity: 0;
            animation-delay: calc(var(--delay, 0ms));
        }
        
        .animate-fade-in-down {
            animation: fade-in-down 0.8s ease-out forwards;
        }
        
        .animate-fade-in-up {
            animation: fade-in-up 0.8s ease-out forwards;
        }
        
        /* Animation delays */
        .animation-delay-500 {
            animation-delay: 0.5s;
        }
        
        .animation-delay-1000 {
            animation-delay: 1s;
        }
        
        .animation-delay-2000 {
            animation-delay: 2s;
        }
        
        .animation-delay-4000 {
            animation-delay: 4s;
        }
        
        /* Particles */
        .particle {
            position: absolute;
            border-radius: 50%;
            animation: float-particle linear infinite;
            pointer-events: none;
        }
        
        /* Background patterns */
        .bg-pattern {
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.2'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }
    </style>
</body>
</html>