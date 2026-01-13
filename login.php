<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

// If already logged in, redirect to dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard/index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $login_input = sanitize($_POST['login_input']); 
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT id, username, password FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$login_input, $login_input]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        header('Location: dashboard/index.php');
        exit();
    } else {
        $error = "Invalid credentials. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In - BudgetTracker</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        teal: { 500: '#2DD4BF', 600: '#0D9488' },
                        navy: { 950: '#050811', 900: '#0B132B', 800: '#1C2541', 700: '#3A506B' }
                    },
                    fontFamily: { 'sans': ['Plus Jakarta Sans', 'sans-serif'] },
                    animation: {
                        'float': 'float 6s ease-in-out infinite',
                        'fadeInUp': 'fadeInUp 0.5s ease-out forwards',
                    }
                }
            }
        }
    </script>
    <style>
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .glass { background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.1); }
        .bg-mesh { background-image: radial-gradient(at 0% 0%, rgba(45, 212, 191, 0.1) 0, transparent 50%), radial-gradient(at 100% 100%, rgba(5, 8, 17, 1) 0, transparent 50%); }
        .input-dark { background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1); color: white; }
        .input-dark:focus { border-color: #2DD4BF; box-shadow: 0 0 15px rgba(45, 212, 191, 0.1); outline: none; }
    </style>
</head>

<body class="bg-navy-950 text-gray-200 antialiased min-h-screen bg-mesh flex items-center justify-center p-6">

    <div class="fixed top-0 left-0 w-full h-full pointer-events-none -z-10">
        <div class="absolute top-1/4 left-1/4 w-64 h-64 bg-teal-500/10 blur-[100px] animate-pulse"></div>
        <div class="absolute bottom-1/4 right-1/4 w-64 h-64 bg-navy-700/20 blur-[100px] animate-pulse"></div>
    </div>

    <div class="max-w-md w-full animate-fadeInUp">
        <div class="flex items-center justify-center space-x-3 mb-10">
            <div class="w-12 h-12 bg-teal-500 rounded-2xl flex items-center justify-center shadow-lg shadow-teal-500/20">
                <i class="fas fa-chart-pie text-navy-900 text-xl"></i>
            </div>
            <span class="text-2xl font-extrabold tracking-tight text-white">Budget<span class="text-teal-500">Tracker</span></span>
        </div>

        <div class="glass rounded-[2.5rem] p-8 md:p-10 shadow-2xl relative overflow-hidden">
            <div class="relative z-10">
                <h2 class="text-3xl font-bold text-white mb-2">Welcome back</h2>
                <p class="text-gray-400 mb-8">Securely access your financial dashboard.</p>

                <?php if (isset($error)): ?>
                    <div class="bg-red-500/10 border border-red-500/20 text-red-400 p-4 rounded-xl mb-6 text-sm flex items-center gap-3">
                        <i class="fas fa-circle-exclamation"></i>
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($_GET['registered'])): ?>
                    <div class="bg-teal-500/10 border border-teal-500/20 text-teal-400 p-4 rounded-xl mb-6 text-sm flex items-center gap-3">
                        <i class="fas fa-check-circle"></i>
                        Registration successful! Please sign in.
                    </div>
                <?php endif; ?>

                <form method="POST" class="space-y-6">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2 ml-1">Username or Email</label>
                        <div class="relative group">
                            <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-500 group-focus-within:text-teal-500 transition">
                                <i class="fas fa-envelope"></i>
                            </span>
                            <input type="text" name="login_input" required 
                                class="input-dark w-full pl-11 pr-4 py-4 rounded-2xl transition-all"
                                placeholder="Enter your details">
                        </div>
                    </div>

                    <div>
                        <div class="flex justify-between mb-2 ml-1">
                            <label class="text-xs font-bold uppercase tracking-widest text-gray-500">Password</label>
                            <a href="#" class="text-xs text-teal-500 hover:text-teal-400 transition">Forgot?</a>
                        </div>
                        <div class="relative group">
                            <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-500 group-focus-within:text-teal-500 transition">
                                <i class="fas fa-lock"></i>
                            </span>
                            <input type="password" name="password" id="password" required 
                                class="input-dark w-full pl-11 pr-12 py-4 rounded-2xl transition-all"
                                placeholder="••••••••">
                            <button type="button" onclick="togglePass()" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-500 hover:text-white transition">
                                <i id="eye-icon" class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" 
                        class="w-full bg-teal-500 hover:bg-teal-400 text-navy-950 font-extrabold py-4 rounded-2xl transition-all transform hover:scale-[1.02] active:scale-[0.98] shadow-xl shadow-teal-500/10">
                        Sign Into Account
                    </button>
                </form>

                <div class="mt-10 pt-8 border-t border-white/5 text-center">
                    <p class="text-gray-500 text-sm">
                        Don't have an account yet? 
                        <a href="register.php" class="text-white font-bold hover:text-teal-500 transition ml-1">Create one free</a>
                    </p>
                </div>
            </div>
        </div>

        <a href="index.php" class="flex items-center justify-center mt-8 text-gray-500 hover:text-white transition text-sm font-medium">
            <i class="fas fa-arrow-left mr-2 text-xs"></i> Back to Home
        </a>
    </div>

    <script>
        function togglePass() {
            const p = document.getElementById('password');
            const i = document.getElementById('eye-icon');
            if (p.type === 'password') {
                p.type = 'text';
                i.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                p.type = 'password';
                i.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }
    </script>
</body>
</html>