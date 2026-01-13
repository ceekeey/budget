<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = sanitize($_POST['username']);
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    $full_name = sanitize($_POST['full_name']);

  
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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Join BudgetTracker</title>
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
                        'fadeInUp': 'fadeInUp 0.5s ease-out forwards',
                    }
                }
            }
        }
    </script>
    <style>
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

    <div class="max-w-xl w-full animate-fadeInUp">
        <div class="flex items-center justify-center space-x-3 mb-10">
            <div class="w-12 h-12 bg-teal-500 rounded-2xl flex items-center justify-center shadow-lg shadow-teal-500/20">
                <i class="fas fa-chart-pie text-navy-900 text-xl"></i>
            </div>
            <span class="text-2xl font-extrabold tracking-tight text-white">Budget<span class="text-teal-500">Tracker</span></span>
        </div>

        <div class="glass rounded-[2.5rem] p-8 md:p-10 shadow-2xl relative overflow-hidden">
            <div class="relative z-10">
                <h2 class="text-3xl font-bold text-white mb-2">Create Account</h2>
                <p class="text-gray-400 mb-8">Start your journey to financial freedom today.</p>

                <form method="POST" class="space-y-5">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2 ml-1">Full Name</label>
                        <div class="relative group">
                            <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-500 group-focus-within:text-teal-500 transition">
                                <i class="fas fa-user"></i>
                            </span>
                            <input type="text" name="full_name" required 
                                class="input-dark w-full pl-11 pr-4 py-4 rounded-2xl transition-all"
                                placeholder="John Doe">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2 ml-1">Username</label>
                            <input type="text" name="username" required 
                                class="input-dark w-full px-5 py-4 rounded-2xl transition-all"
                                placeholder="johndoe">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2 ml-1">Email</label>
                            <input type="email" name="email" required 
                                class="input-dark w-full px-5 py-4 rounded-2xl transition-all"
                                placeholder="john@example.com">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2 ml-1">Create Password</label>
                        <div class="relative group">
                            <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-500 group-focus-within:text-teal-500 transition">
                                <i class="fas fa-lock"></i>
                            </span>
                            <input type="password" name="password" id="password" required 
                                class="input-dark w-full pl-11 pr-12 py-4 rounded-2xl transition-all"
                                placeholder="••••••••">
                        </div>
                    </div>

                    <button type="submit" 
                        class="w-full bg-teal-500 hover:bg-teal-400 text-navy-950 font-extrabold py-4 rounded-2xl transition-all transform hover:scale-[1.02] active:scale-[0.98] shadow-xl shadow-teal-500/10 mt-4">
                        Create My Account
                    </button>
                </form>

                <div class="mt-10 pt-8 border-t border-white/5 text-center">
                    <p class="text-gray-500 text-sm">
                        Already have an account? 
                        <a href="login.php" class="text-white font-bold hover:text-teal-500 transition ml-1">Sign in here</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>