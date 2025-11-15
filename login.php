<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

// Check if user is already logged in
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['user_id'])) {
    header('Location: dashboard/index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = sanitize($_POST['username']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        session_start();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['full_name'] = $user['full_name'];

        header('Location: dashboard/index.php');
        exit();
    } else {
        $error = "Invalid username or password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome Back - BudgetTracker</title>
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

        .input-focus:focus {
            border-color: #FF6B35;
            box-shadow: 0 0 0 3px rgba(255, 107, 53, 0.1);
        }

        .gradient-bg {
            background: linear-gradient(135deg, #1a202c 0%, #2d3748 50%, #4a5568 100%);
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
            <!-- Left Side - Welcome Message -->
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
                            Welcome Back to
                            <span class="text-orange-500">Your Finances</span>
                        </h1>

                        <p class="text-lg text-gray-600 mb-8 leading-relaxed">
                            Continue your journey to financial freedom. Access your budgets, track expenses, and stay on
                            top of your financial goals.
                        </p>

                        <!-- Stats Overview -->
                        <div class="grid grid-cols-2 gap-6 mb-8">
                            <div class="bg-orange-50 rounded-2xl p-6 text-center border border-orange-100">
                                <div
                                    class="w-12 h-12 bg-orange-500 rounded-full flex items-center justify-center mx-auto mb-3 shadow-md">
                                    <i class="fas fa-chart-line text-white text-lg"></i>
                                </div>
                                <h3 class="font-semibold text-gray-800 mb-1">Smart Tracking</h3>
                                <p class="text-sm text-gray-600">Real-time budget monitoring</p>
                            </div>
                            <div class="bg-green-50 rounded-2xl p-6 text-center border border-green-100">
                                <div
                                    class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center mx-auto mb-3 shadow-md">
                                    <i class="fas fa-piggy-bank text-white text-lg"></i>
                                </div>
                                <h3 class="font-semibold text-gray-800 mb-1">Save More</h3>
                                <p class="text-sm text-gray-600">Achieve your savings goals</p>
                            </div>
                        </div>

                        <!-- Features List -->
                        <div class="space-y-4 mb-8">
                            <div class="flex items-center space-x-3">
                                <div
                                    class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center shadow-sm">
                                    <i class="fas fa-bolt text-blue-600 text-sm"></i>
                                </div>
                                <span class="text-gray-700">Quick access to your dashboard</span>
                            </div>
                            <div class="flex items-center space-x-3">
                                <div
                                    class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center shadow-sm">
                                    <i class="fas fa-chart-pie text-green-600 text-sm"></i>
                                </div>
                                <span class="text-gray-700">Updated financial insights</span>
                            </div>
                            <div class="flex items-center space-x-3">
                                <div
                                    class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center shadow-sm">
                                    <i class="fas fa-bell text-purple-600 text-sm"></i>
                                </div>
                                <span class="text-gray-700">Budget alerts and notifications</span>
                            </div>
                        </div>

                        <!-- Security Notice -->
                        <div
                            class="bg-gradient-to-r from-green-50 to-blue-50 rounded-2xl p-6 border-l-4 border-green-500 shadow-sm">
                            <div class="flex items-center space-x-3">
                                <div
                                    class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center shadow-md">
                                    <i class="fas fa-shield-alt text-white"></i>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-800">Secure Access</h4>
                                    <p class="text-sm text-gray-600">Your financial data is protected with bank-level
                                        security</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side - Login Form -->
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
                            <h2 class="text-3xl font-bold text-gray-800 mb-2">Welcome Back</h2>
                            <p class="text-gray-600">Sign in to your BudgetTracker account</p>
                        </div>

                        <?php if (isset($_GET['registered'])): ?>
                            <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-lg mb-6 animate-bounceIn">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-check-circle text-green-500"></i>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-green-700 text-sm">Registration successful! Please sign i
                                            n to continue.</p>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

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
                            <!-- Username -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2" for="username">
                                    <i class="fas fa-user text-orange-500 mr-2"></i>Username
                                </label>
                                <div class="relative">
                                    <input type="text" id="username" name="username" required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none input-focus transition duration-300 bg-white"
                                        placeholder="Enter your username"
                                        value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"
                                        autocomplete="username">
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                        <i class="fas fa-user-circle text-gray-400"></i>
                                    </div>
                                </div>
                            </div>

                            <!-- Password -->
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <label class="block text-sm font-medium text-gray-700" for="password">
                                        <i class="fas fa-lock text-orange-500 mr-2"></i>Password
                                    </label>
                                    <a href="#"
                                        class="text-sm text-orange-500 hover:text-orange-600 font-medium transition duration-300">
                                        Forgot password?
                                    </a>
                                </div>
                                <div class="relative">
                                    <input type="password" id="password" name="password" required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none input-focus transition duration-300 bg-white"
                                        placeholder="Enter your password" autocomplete="current-password">
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center cursor-pointer"
                                        onclick="togglePassword('password')">
                                        <i class="fas fa-eye text-gray-400"></i>
                                    </div>
                                </div>
                            </div>

                            <!-- Remember Me -->
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <input type="checkbox" id="remember" name="remember"
                                        class="w-4 h-4 text-orange-500 border-gray-300 rounded focus:ring-orange-500">
                                    <label for="remember" class="text-sm text-gray-600">
                                        Remember me
                                    </label>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit"
                                class="w-full bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white font-semibold py-4 px-6 rounded-xl transition duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl relative overflow-hidden group">
                                <span class="relative z-10">
                                    <i class="fas fa-sign-in-alt mr-2"></i>Sign In
                                </span>
                                <div
                                    class="absolute inset-0 bg-white opacity-0 group-hover:opacity-10 transition duration-300">
                                </div>
                            </button>

                            <!-- Demo Account Info
                            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                                <div class="flex items-center space-x-2 text-blue-700 mb-2">
                                    <i class="fas fa-info-circle"></i>
                                    <span class="text-sm font-medium">Demo Account</span>
                                </div>
                                <p class="text-blue-600 text-xs">
                                    Use username: <strong>demo</strong> and password: <strong>demo123</strong> to test
                                    the application.
                                </p>
                            </div> -->
                        </form>

                        <!-- Register Link -->
                        <div class="text-center mt-8 pt-6 border-t border-gray-200">
                            <p class="text-gray-600">
                                Don't have an account?
                                <a href="register.php"
                                    class="text-orange-500 hover:text-orange-600 font-semibold transition duration-300 inline-flex items-center">
                                    Create one here <i class="fas fa-arrow-right ml-1 text-sm"></i>
                                </a>
                            </p>
                        </div>

                        <!-- Security Notice -->
                        <div class="mt-6 text-center">
                            <div
                                class="flex items-center justify-center space-x-2 text-sm text-gray-500 bg-gray-50 p-3 rounded-lg">
                                <i class="fas fa-shield-alt text-green-500"></i>
                                <span>Secure SSL encrypted connection</span>
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

        // Auto-focus on username field
        document.addEventListener('DOMContentLoaded', function () {
            document.getElementById('username').focus();
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