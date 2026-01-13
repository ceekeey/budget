<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BudgetTracker - Master Your Money Flow</title>
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
                        'pulse-slow': 'pulse 4s cubic-bezier(0.4, 0, 0.6, 1) infinite',
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
        .glass { background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.1); }
        .teal-glow { box-shadow: 0 0 20px rgba(45, 212, 191, 0.2); }
        .text-gradient { background: linear-gradient(135deg, #2DD4BF 0%, #5BC0BE 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .bg-mesh { background-image: radial-gradient(at 0% 0%, rgba(45, 212, 191, 0.15) 0, transparent 50%), radial-gradient(at 100% 100%, rgba(28, 37, 65, 1) 0, transparent 50%); }
    </style>
</head>

<body class="bg-navy-950 text-gray-200 antialiased">

    <nav class="fixed w-full z-50 border-b border-white/5 bg-navy-950/80 backdrop-blur-lg">
        <div class="container mx-auto px-6 py-4 flex justify-between items-center">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-teal-500 rounded-xl flex items-center justify-center shadow-lg shadow-teal-500/20">
                    <i class="fas fa-chart-pie text-navy-900 text-lg"></i>
                </div>
                <span class="text-xl font-extrabold tracking-tight text-white">Budget<span class="text-teal-500">Tracker</span></span>
            </div>

            <div class="hidden md:flex items-center space-x-10">
                <a href="#features" class="text-sm font-medium text-gray-400 hover:text-teal-500 transition">Features</a>
                <a href="#how-it-works" class="text-sm font-medium text-gray-400 hover:text-teal-500 transition">Process</a>
                <a href="#testimonials" class="text-sm font-medium text-gray-400 hover:text-teal-500 transition">Community</a>
            </div>

            <div class="flex items-center space-x-5">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="dashboard/index.php" class="bg-teal-500 hover:bg-teal-400 text-navy-950 px-5 py-2.5 rounded-full font-bold transition transform hover:scale-105">Dashboard</a>
                <?php else: ?>
                    <a href="login.php" class="hidden sm:block text-sm font-bold text-gray-300 hover:text-white transition">Sign In</a>
                    <a href="register.php" class="bg-white/10 hover:bg-white/20 border border-white/10 px-5 py-2.5 rounded-full text-sm font-bold transition">Get Started</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <section class="relative pt-32 pb-20 overflow-hidden bg-mesh">
        <div class="container mx-auto px-6 relative z-10">
            <div class="flex flex-col lg:flex-row items-center gap-16">
                <div class="lg:w-1/2 text-center lg:text-left">
                    <div class="inline-flex items-center space-x-2 bg-teal-500/10 border border-teal-500/20 px-4 py-2 rounded-full mb-6">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-teal-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-teal-500"></span>
                        </span>
                        <span class="text-teal-500 text-xs font-bold uppercase tracking-widest">v2.0 Now Live</span>
                    </div>
                    <h1 class="text-6xl lg:text-7xl font-extrabold text-white mb-8 leading-[1.1]">
                        Master Your <br/>
                        <span class="text-gradient">Money Flow.</span>
                    </h1>
                    <p class="text-lg text-gray-400 mb-10 leading-relaxed max-w-xl mx-auto lg:mx-0">
                        Stop wondering where your money went. Join 10,000+ Nigerians using BudgetTracker to automate their savings and crush financial goals.
                    </p>
                    <div class="flex flex-col sm:flex-row justify-center lg:justify-start gap-4">
                        <a href="register.php" class="bg-teal-500 hover:bg-teal-400 text-navy-950 px-8 py-4 rounded-2xl font-bold text-lg transition-all shadow-xl shadow-teal-500/20">
                            Start Free Journey
                        </a>
                        <a href="#how-it-works" class="px-8 py-4 rounded-2xl font-bold text-lg border border-white/10 hover:bg-white/5 transition-all text-white">
                            See How it Works
                        </a>
                    </div>
                </div>

                <div class="lg:w-1/2 animate-float">
                    <div class="glass p-4 rounded-[2.5rem] relative">
                        <div class="absolute -top-10 -right-10 w-32 h-32 bg-teal-500/20 blur-3xl rounded-full"></div>
                        <div class="bg-navy-900 rounded-[2rem] p-8 border border-white/5">
                            <div class="flex justify-between items-center mb-10">
                                <div>
                                    <p class="text-gray-500 text-xs uppercase tracking-tighter mb-1">Total Balance</p>
                                    <h3 class="text-3xl font-bold text-white tracking-tight">₦450,000.00</h3>
                                </div>
                                <div class="w-12 h-12 bg-teal-500/10 rounded-2xl flex items-center justify-center">
                                    <i class="fas fa-wallet text-teal-500"></i>
                                </div>
                            </div>
                            <div class="space-y-6">
                                <div class="bg-navy-800/50 p-5 rounded-2xl border border-white/5">
                                    <div class="flex justify-between items-center mb-3">
                                        <span class="text-sm font-medium">Entertainment</span>
                                        <span class="text-teal-500 text-sm font-bold">80%</span>
                                    </div>
                                    <div class="h-2 w-full bg-navy-700 rounded-full overflow-hidden">
                                        <div class="h-full bg-teal-500" style="width: 80%"></div>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="p-4 bg-navy-800/30 rounded-2xl border border-white/5">
                                        <p class="text-gray-500 text-[10px] uppercase mb-1">Income</p>
                                        <p class="text-white font-bold">+₦120k</p>
                                    </div>
                                    <div class="p-4 bg-navy-800/30 rounded-2xl border border-white/5">
                                        <p class="text-gray-500 text-[10px] uppercase mb-1">Spent</p>
                                        <p class="text-red-400 font-bold">-₦45k</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-12 border-y border-white/5 bg-navy-900/50">
        <div class="container mx-auto px-6">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                <div class="text-center">
                    <p class="text-3xl font-bold text-white mb-1">10k+</p>
                    <p class="text-gray-500 text-sm">Active Users</p>
                </div>
                <div class="text-center">
                    <p class="text-3xl font-bold text-white mb-1">₦5.2M</p>
                    <p class="text-gray-500 text-sm">Monthly Volume</p>
                </div>
                <div class="text-center">
                    <p class="text-3xl font-bold text-white mb-1">98%</p>
                    <p class="text-gray-500 text-sm">Satisfaction</p>
                </div>
                <div class="text-center">
                    <p class="text-3xl font-bold text-white mb-1">24/7</p>
                    <p class="text-gray-500 text-sm">Support</p>
                </div>
            </div>
        </div>
    </section>

    <section id="features" class="py-24 relative">
        <div class="container mx-auto px-6">
            <div class="max-w-3xl mx-auto text-center mb-20">
                <h2 class="text-4xl font-bold text-white mb-6 tracking-tight">Everything You Need to Scale Your Wealth</h2>
                <p class="text-gray-400 text-lg">We’ve built a suite of tools that work together to simplify your financial life.</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="glass p-10 rounded-[2rem] hover:bg-white/5 transition group">
                    <div class="w-14 h-14 bg-teal-500/10 rounded-2xl flex items-center justify-center mb-8 group-hover:scale-110 transition">
                        <i class="fas fa-layer-group text-teal-500 text-xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-4">Smart Categorization</h3>
                    <p class="text-gray-400 leading-relaxed mb-6">Automatically organize your spending into groups like Food, Rent, and Leisure.</p>
                    <ul class="space-y-3 text-sm text-gray-500">
                        <li class="flex items-center gap-2"><i class="fas fa-check-circle text-teal-500"></i> Auto-tagging</li>
                        <li class="flex items-center gap-2"><i class="fas fa-check-circle text-teal-500"></i> Custom Categories</li>
                    </ul>
                </div>
                <div class="glass p-10 rounded-[2rem] hover:bg-white/5 transition group">
                    <div class="w-14 h-14 bg-teal-500/10 rounded-2xl flex items-center justify-center mb-8 group-hover:scale-110 transition">
                        <i class="fas fa-bell text-teal-500 text-xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-4">Smart Alerts</h3>
                    <p class="text-gray-400 leading-relaxed mb-6">Get notified when you are approaching your budget limits before you overspend.</p>
                    <ul class="space-y-3 text-sm text-gray-500">
                        <li class="flex items-center gap-2"><i class="fas fa-check-circle text-teal-500"></i> Spending Alerts</li>
                        <li class="flex items-center gap-2"><i class="fas fa-check-circle text-teal-500"></i> Goal Milestones</li>
                    </ul>
                </div>
                <div class="glass p-10 rounded-[2rem] hover:bg-white/5 transition group">
                    <div class="w-14 h-14 bg-teal-500/10 rounded-2xl flex items-center justify-center mb-8 group-hover:scale-110 transition">
                        <i class="fas fa-file-invoice-dollar text-teal-500 text-xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-4">Detailed Reports</h3>
                    <p class="text-gray-400 leading-relaxed mb-6">Export PDF and CSV reports of your monthly performance for accounting.</p>
                    <ul class="space-y-3 text-sm text-gray-500">
                        <li class="flex items-center gap-2"><i class="fas fa-check-circle text-teal-500"></i> PDF/CSV Export</li>
                        <li class="flex items-center gap-2"><i class="fas fa-check-circle text-teal-500"></i> Historical Trends</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <section id="testimonials" class="py-24 bg-navy-900/30">
        <div class="container mx-auto px-6">
            <h2 class="text-center text-3xl font-bold mb-16">Trusted by Thousands</h2>
            <div class="grid md:grid-cols-3 gap-8">
                <div class="glass p-8 rounded-3xl">
                    <div class="flex gap-1 text-teal-500 mb-4">
                        <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                    </div>
                    <p class="text-gray-400 italic mb-6">"Finally an app that understands the Nigerian economy. The naira tracking is perfect."</p>
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 bg-teal-500/20 rounded-full"></div>
                        <div>
                            <p class="text-white font-bold text-sm">Ade Johnson</p>
                            <p class="text-gray-500 text-xs">Business Owner</p>
                        </div>
                    </div>
                </div>
                </div>
        </div>
    </section>

    <section class="py-24">
        <div class="container mx-auto px-6">
            <div class="bg-gradient-to-r from-teal-500 to-teal-600 rounded-[3rem] p-12 text-center relative overflow-hidden">
                <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0); background-size: 24px 24px;"></div>
                <div class="relative z-10">
                    <h2 class="text-navy-950 text-4xl font-extrabold mb-6">Take Control of Your Future Today.</h2>
                    <p class="text-navy-900/70 text-lg mb-10 max-w-xl mx-auto font-medium">No hidden fees. No credit cards. Just pure financial freedom at your fingertips.</p>
                    <a href="register.php" class="bg-navy-950 text-white px-10 py-4 rounded-2xl font-bold text-lg hover:scale-105 transition shadow-2xl">
                        Create Your Free Account
                    </a>
                </div>
            </div>
        </div>
    </section>

    <footer class="py-20 border-t border-white/5">
        <div class="container mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-12 mb-16">
                <div class="col-span-1 md:col-span-1">
                    <div class="flex items-center space-x-3 mb-6">
                        <div class="w-8 h-8 bg-teal-500 rounded-lg flex items-center justify-center">
                            <i class="fas fa-chart-pie text-navy-900 text-sm"></i>
                        </div>
                        <span class="text-xl font-bold text-white tracking-tight">BudgetTracker</span>
                    </div>
                    <p class="text-gray-500 text-sm leading-relaxed">Modern personal finance tools designed for the next generation of savers.</p>
                </div>
                <div>
                    <h4 class="text-white font-bold mb-6">Product</h4>
                    <ul class="space-y-4 text-sm text-gray-500">
                        <li><a href="#" class="hover:text-teal-500 transition">Mobile App</a></li>
                        <li><a href="#" class="hover:text-teal-500 transition">Security</a></li>
                        <li><a href="#" class="hover:text-teal-500 transition">Pricing</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-white font-bold mb-6">Support</h4>
                    <ul class="space-y-4 text-sm text-gray-500">
                        <li><a href="#" class="hover:text-teal-500 transition">Help Center</a></li>
                        <li><a href="#" class="hover:text-teal-500 transition">API Docs</a></li>
                        <li><a href="#" class="hover:text-teal-500 transition">Privacy Policy</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-white font-bold mb-6">Social</h4>
                    <div class="flex gap-4">
                        <a href="#" class="w-10 h-10 rounded-xl bg-navy-900 border border-white/5 flex items-center justify-center hover:border-teal-500/50 transition"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="w-10 h-10 rounded-xl bg-navy-900 border border-white/5 flex items-center justify-center hover:border-teal-500/50 transition"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
            </div>
            <div class="text-center text-gray-600 text-xs border-t border-white/5 pt-10">
                &copy; 2026 BudgetTracker. Built By ❤️ Kefas.
            </div>
        </div>
    </footer>

    <script>
        // Smooth scroll and visibility observer (as in original)
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('opacity-100');
                    entry.target.classList.remove('opacity-0', 'translate-y-10');
                }
            });
        }, { threshold: 0.1 });

        // Apply fade-in to sections
        document.querySelectorAll('section').forEach(section => {
            section.classList.add('transition-all', 'duration-1000', 'opacity-0', 'translate-y-10');
            observer.observe(section);
        });
    </script>
</body>
</html>