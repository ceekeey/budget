<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = sanitize($_POST['username']);
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $full_name = sanitize($_POST['full_name']);

    if ($password !== $confirm_password) {
        $error = "Passwords do not match!";
    } else {
        // Check if username or email already exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);

        if ($stmt->rowCount() > 0) {
            $error = "Username or email already exists!";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $pdo->prepare("INSERT INTO users (username, email, password, full_name) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$username, $email, $hashed_password, $full_name])) {
                header('Location: login.php?registered=1');
                exit();
            } else {
                $error = "Registration failed! Please try again.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Your Account - BudgetTracker</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        orange: {
                            500: '#FF6B35',
                            600: '#E55A2B',
                        }
                    },
                    fontFamily: {
                        'inter': ['Inter', 'sans-serif'],
                    },
                    animation: {
                        'fadeIn': 'fadeIn 0.6s ease-in-out',
                        'slideInUp': 'slideInUp 0.8s ease-out',
                        'bounceIn': 'bounceIn 0.6s ease-out',
                        'float': 'float 6s ease-in-out infinite',
                    }
                }
            }
        }
    </script>
    <style>
        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes bounceIn {
            0% {
                opacity: 0;
                transform: scale(0.3);
            }

            50% {
                opacity: 1;
                transform: scale(1.05);
            }

            70% {
                transform: scale(0.9);
            }

            100% {
                opacity: 1;
                transform: scale(1);
            }
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0px) rotate(0deg);
            }

            50% {
                transform: translateY(-20px) rotate(5deg);
            }
        }

        .gradient-bg {
            background: linear-gradient(135deg, #1a202c 0%, #2d3748 50%, #4a5568 100%);
        }

        .input-focus:focus {
            border-color: #FF6B35;
            box-shadow: 0 0 0 3px rgba(255, 107, 53, 0.1);
        }

        .password-strength {
            height: 4px;
            transition: all 0.3s ease;
        }

        .image-container {
            position: relative;
            overflow: hidden;
            border-radius: 20px;
        }

        .image-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(255, 107, 53, 0.1) 0%, rgba(229, 90, 43, 0.05) 100%);
            z-index: 1;
        }
    </style>
</head>

<body class="font-inter bg-gray-50 min-h-screen">
    <!-- Background Pattern -->
    <div class="fixed inset-0 bg-gradient-to-br from-orange-50 via-white to-gray-100 -z-10"></div>

    <!-- Floating Elements -->
    <div class="fixed top-10 left-10 w-20 h-20 bg-orange-100 rounded-full opacity-20 animate-float -z-10"></div>
    <div class="fixed bottom-20 right-20 w-16 h-16 bg-orange-200 rounded-full opacity-30 animate-float -z-10"
        style="animation-delay: 2s;"></div>
    <div class="fixed top-1/3 right-1/4 w-12 h-12 bg-orange-300 rounded-full opacity-20 animate-float -z-10"
        style="animation-delay: 4s;"></div>

    <!-- Main Content -->
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="max-w-6xl w-full grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">
            <!-- Left Side - Brand & Info -->
            <div class="hidden lg:block animate-fadeIn">
                <div class="bg-white rounded-3xl shadow-2xl p-8 relative overflow-hidden">
                    <!-- Decorative Background -->
                    <div class="absolute top-0 right-0 w-32 h-32 bg-orange-500 rounded-full -mr-16 -mt-16 opacity-10">
                    </div>
                    <div class="absolute bottom-0 left-0 w-24 h-24 bg-orange-500 rounded-full -ml-12 -mb-12 opacity-10">
                    </div>

                    <div class="relative z-10">
                        <div class="flex items-center space-x-3 mb-8">
                            <div class="w-12 h-12 bg-orange-500 rounded-xl flex items-center justify-center shadow-lg">
                                <i class="fas fa-wallet text-white text-xl"></i>
                            </div>
                            <span class="text-3xl font-bold text-gray-800">BudgetTracker</span>
                        </div>

                        <h1 class="text-4xl font-bold text-gray-800 mb-6 leading-tight">
                            Start Your Journey to
                            <span class="text-orange-500">Financial Freedom</span>
                        </h1>

                        <p class="text-lg text-gray-600 mb-8 leading-relaxed">
                            Join thousands of users who have transformed their financial lives with BudgetTracker.
                            Take control of your money and build a better future.
                        </p>

                        <!-- Hero Image -->
                        <div
                            class="image-container mb-8 bg-gradient-to-br from-orange-100 to-orange-50 p-6 rounded-2xl border border-orange-200">
                            <div class="grid grid-cols-2 gap-4">
                                <div class="text-center">
                                    <div
                                        class="bg-white rounded-xl p-4 shadow-lg mb-3 transform hover:scale-105 transition duration-300">
                                        <i class="fas fa-chart-pie text-orange-500 text-2xl mb-2"></i>
                                        <p class="text-sm font-medium text-gray-700">Budget Analysis</p>
                                    </div>
                                    <div
                                        class="bg-white rounded-xl p-4 shadow-lg transform hover:scale-105 transition duration-300">
                                        <i class="fas fa-receipt text-orange-500 text-2xl mb-2"></i>
                                        <p class="text-sm font-medium text-gray-700">Expense Tracking</p>
                                    </div>
                                </div>
                                <div class="text-center">
                                    <div
                                        class="bg-white rounded-xl p-4 shadow-lg mb-3 transform hover:scale-105 transition duration-300">
                                        <i class="fas fa-piggy-bank text-green-500 text-2xl mb-2"></i>
                                        <p class="text-sm font-medium text-gray-700">Savings Goals</p>
                                    </div>
                                    <div
                                        class="bg-white rounded-xl p-4 shadow-lg transform hover:scale-105 transition duration-300">
                                        <i class="fas fa-chart-line text-blue-500 text-2xl mb-2"></i>
                                        <p class="text-sm font-medium text-gray-700">Progress Reports</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Features List -->
                        <div class="space-y-4 mb-8">
                            <div class="flex items-center space-x-3">
                                <div
                                    class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center shadow-sm">
                                    <i class="fas fa-check text-green-600 text-sm"></i>
                                </div>
                                <span class="text-gray-700">Track expenses in real-time</span>
                            </div>
                            <div class="flex items-center space-x-3">
                                <div
                                    class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center shadow-sm">
                                    <i class="fas fa-check text-green-600 text-sm"></i>
                                </div>
                                <span class="text-gray-700">Set and manage budgets easily</span>
                            </div>
                            <div class="flex items-center space-x-3">
                                <div
                                    class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center shadow-sm">
                                    <i class="fas fa-check text-green-600 text-sm"></i>
                                </div>
                                <span class="text-gray-700">Get detailed financial reports</span>
                            </div>
                            <div class="flex items-center space-x-3">
                                <div
                                    class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center shadow-sm">
                                    <i class="fas fa-check text-green-600 text-sm"></i>
                                </div>
                                <span class="text-gray-700">Free forever - no hidden costs</span>
                            </div>
                        </div>

                        <!-- Testimonial -->
                        <div
                            class="bg-gradient-to-r from-orange-50 to-orange-100 rounded-2xl p-6 border-l-4 border-orange-500 shadow-sm">
                            <div class="flex items-center space-x-3 mb-3">
                                <div
                                    class="w-12 h-12 bg-orange-500 rounded-full flex items-center justify-center shadow-md">
                                    <span class="text-white font-bold text-sm">AJ</span>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-800">Adeola Johnson</h4>
                                    <p class="text-sm text-gray-600">Small Business Owner</p>
                                </div>
                            </div>
                            <p class="text-gray-700 italic">"BudgetTracker helped me understand my spending and save
                                ₦50,000 in the first month alone!"</p>
                            <div class="flex text-orange-500 mt-3">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side - Registration Form -->
            <div class="animate-slideInUp">
                <div class="bg-white rounded-3xl shadow-2xl p-8 lg:p-10 relative overflow-hidden">
                    <!-- Form Background Decorations -->
                    <div class="absolute top-0 right-0 w-20 h-20 bg-orange-500 rounded-full -mr-10 -mt-10 opacity-5">
                    </div>
                    <div class="absolute bottom-0 left-0 w-16 h-16 bg-orange-500 rounded-full -ml-8 -mb-8 opacity-5">
                    </div>

                    <div class="relative z-10">
                        <div class="text-center mb-8">
                            <div class="lg:hidden flex justify-center mb-6">
                                <div
                                    class="w-20 h-20 bg-gradient-to-br from-orange-500 to-orange-600 rounded-2xl flex items-center justify-center shadow-lg">
                                    <i class="fas fa-wallet text-white text-3xl"></i>
                                </div>
                            </div>
                            <h2 class="text-3xl font-bold text-gray-800 mb-2">Create Your Account</h2>
                            <p class="text-gray-600">Join BudgetTracker and take control of your finances</p>
                        </div>

                        <?php if (isset($error)): ?>
                            <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-lg mb-6 animate-bounceIn">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-exclamation-circle text-red-500"></i>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-red-700 text-sm"><?php echo $error; ?></p>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <form method="POST" class="space-y-6">
                            <!-- Full Name -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2" for="full_name">
                                    <i class="fas fa-user text-orange-500 mr-2"></i>Full Name
                                </label>
                                <div class="relative">
                                    <input type="text" id="full_name" name="full_name" required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none input-focus transition duration-300 bg-white"
                                        placeholder="Enter your full name"
                                        value="<?php echo isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : ''; ?>">
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                        <i class="fas fa-user text-gray-400"></i>
                                    </div>
                                </div>
                            </div>

                            <!-- Username -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2" for="username">
                                    <i class="fas fa-at text-orange-500 mr-2"></i>Username
                                </label>
                                <div class="relative">
                                    <input type="text" id="username" name="username" required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none input-focus transition duration-300 bg-white"
                                        placeholder="Choose a username"
                                        value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                        <i class="fas fa-user-circle text-gray-400"></i>
                                    </div>
                                </div>
                            </div>

                            <!-- Email -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2" for="email">
                                    <i class="fas fa-envelope text-orange-500 mr-2"></i>Email Address
                                </label>
                                <div class="relative">
                                    <input type="email" id="email" name="email" required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none input-focus transition duration-300 bg-white"
                                        placeholder="your@email.com"
                                        value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                        <i class="fas fa-envelope text-gray-400"></i>
                                    </div>
                                </div>
                            </div>

                            <!-- Password -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2" for="password">
                                    <i class="fas fa-lock text-orange-500 mr-2"></i>Password
                                </label>
                                <div class="relative">
                                    <input type="password" id="password" name="password" required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none input-focus transition duration-300 bg-white"
                                        placeholder="Create a strong password">
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center cursor-pointer"
                                        onclick="togglePassword('password')">
                                        <i class="fas fa-eye text-gray-400"></i>
                                    </div>
                                </div>
                                <div class="mt-2 grid grid-cols-4 gap-1">
                                    <div id="strength-1" class="password-strength bg-gray-200 rounded"></div>
                                    <div id="strength-2" class="password-strength bg-gray-200 rounded"></div>
                                    <div id="strength-3" class="password-strength bg-gray-200 rounded"></div>
                                    <div id="strength-4" class="password-strength bg-gray-200 rounded"></div>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Use 8+ characters with numbers and symbols</p>
                            </div>

                            <!-- Confirm Password -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2" for="confirm_password">
                                    <i class="fas fa-lock text-orange-500 mr-2"></i>Confirm Password
                                </label>
                                <div class="relative">
                                    <input type="password" id="confirm_password" name="confirm_password" required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none input-focus transition duration-300 bg-white"
                                        placeholder="Confirm your password">
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center cursor-pointer"
                                        onclick="togglePassword('confirm_password')">
                                        <i class="fas fa-eye text-gray-400"></i>
                                    </div>
                                </div>
                            </div>

                            <!-- Terms Agreement -->
                            <div class="flex items-start space-x-3 bg-orange-50 p-4 rounded-xl">
                                <input type="checkbox" id="terms" name="terms" required
                                    class="w-4 h-4 text-orange-500 border-gray-300 rounded focus:ring-orange-500 mt-1">
                                <label for="terms" class="text-sm text-gray-600">
                                    I agree to the <a href="#"
                                        class="text-orange-500 hover:text-orange-600 font-medium">Terms of Service</a>
                                    and <a href="#" class="text-orange-500 hover:text-orange-600 font-medium">Privacy
                                        Policy</a>.
                                    I understand that my data will be processed in accordance with these policies.
                                </label>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit"
                                class="w-full bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white font-semibold py-4 px-6 rounded-xl transition duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl relative overflow-hidden group">
                                <span class="relative z-10">
                                    <i class="fas fa-user-plus mr-2"></i>Create Account
                                </span>
                                <div
                                    class="absolute inset-0 bg-white opacity-0 group-hover:opacity-10 transition duration-300">
                                </div>
                            </button>
                        </form>

                        <!-- Login Link -->
                        <div class="text-center mt-8 pt-6 border-t border-gray-200">
                            <p class="text-gray-600">
                                Already have an account?
                                <a href="login.php"
                                    class="text-orange-500 hover:text-orange-600 font-semibold transition duration-300 inline-flex items-center">
                                    Sign in here <i class="fas fa-arrow-right ml-1 text-sm"></i>
                                </a>
                            </p>
                        </div>

                        <!-- Security Notice -->
                        <div class="mt-6 text-center">
                            <div
                                class="flex items-center justify-center space-x-2 text-sm text-gray-500 bg-green-50 p-3 rounded-lg">
                                <i class="fas fa-shield-alt text-green-500"></i>
                                <span>Your financial data is securely encrypted and protected</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Toggle password visibility
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const icon = field.parentElement.querySelector('i');

            if (field.type === 'password') {
                field.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                field.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        // Password strength indicator
        document.getElementById('password').addEventListener('input', function (e) {
            const password = e.target.value;
            const strengthBars = [
                document.getElementById('strength-1'),
                document.getElementById('strength-2'),
                document.getElementById('strength-3'),
                document.getElementById('strength-4')
            ];

            // Reset all bars
            strengthBars.forEach(bar => {
                bar.style.backgroundColor = '#e5e7eb';
            });

            let strength = 0;

            // Length check
            if (password.length >= 8) strength++;

            // Contains numbers
            if (/\d/.test(password)) strength++;

            // Contains special characters
            if (/[!@#$%^&*(),.?":{}|<>]/.test(password)) strength++;

            // Contains uppercase and lowercase
            if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;

            // Update strength bars
            for (let i = 0; i < strength; i++) {
                let color;
                if (strength === 1) color = '#ef4444'; // red
                else if (strength === 2) color = '#f59e0b'; // yellow
                else if (strength === 3) color = '#10b981'; // green
                else if (strength === 4) color = '#059669'; // dark green

                strengthBars[i].style.backgroundColor = color;
            }
        });

        // Form validation animation
        document.querySelector('form').addEventListener('submit', function (e) {
            const inputs = this.querySelectorAll('input[required]');
            let valid = true;

            inputs.forEach(input => {
                if (!input.value.trim()) {
                    input.classList.add('border-red-500');
                    valid = false;

                    // Shake animation for invalid fields
                    input.style.animation = 'shake 0.5s ease-in-out';
                    setTimeout(() => {
                        input.style.animation = '';
                    }, 500);
                } else {
                    input.classList.remove('border-red-500');
                }
            });

            if (!valid) {
                e.preventDefault();
            }
        });

        // Add shake animation
        const style = document.createElement('style');
        style.textContent = `
            @keyframes shake {
                0%, 100% { transform: translateX(0); }
                25% { transform: translateX(-5px); }
                75% { transform: translateX(5px); }
            }
        `;
        document.head.appendChild(style);

        // Add floating animation to decorative elements
        document.addEventListener('DOMContentLoaded', function () {
            const floatingElements = document.querySelectorAll('.fixed');
            floatingElements.forEach((el, index) => {
                el.style.animationDelay = `${index * 2}s`;
            });
        });
    </script>
</body>

</html>