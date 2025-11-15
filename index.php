<?php
// Start session only if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BudgetTracker - Take Control of Your Finances</title>
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
                            700: '#CC4A23',
                        },
                        dark: {
                            900: '#1a202c',
                            800: '#2d3748',
                        }
                    },
                    fontFamily: {
                        'inter': ['Inter', 'sans-serif'],
                    },
                    animation: {
                        'float': 'float 6s ease-in-out infinite',
                        'fadeIn': 'fadeIn 0.8s ease-in-out',
                        'slideInUp': 'slideInUp 0.8s ease-out',
                        'bounceIn': 'bounceIn 1s ease-out',
                    }
                }
            }
        }
    </script>
    <style>
        @keyframes float {

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-20px);
            }
        }

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

        .gradient-bg {
            background: linear-gradient(135deg, #1a202c 0%, #2d3748 50%, #4a5568 100%);
        }

        .hero-gradient {
            background: linear-gradient(135deg, #FF6B35 0%, #E55A2B 50%, #CC4A23 100%);
        }

        .card-hover {
            transition: all 0.3s ease;
        }

        .card-hover:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }

        .feature-icon {
            background: linear-gradient(135deg, #FF6B35, #E55A2B);
        }

        .stat-number {
            background: linear-gradient(135deg, #FF6B35, #E55A2B);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
    </style>
</head>

<body class="font-inter bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg sticky top-0 z-50">
        <div class="container mx-auto px-6 py-4">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-2">
                    <div class="w-10 h-10 bg-orange-500 rounded-xl flex items-center justify-center">
                        <i class="fas fa-wallet text-white text-lg"></i>
                    </div>
                    <span class="text-2xl font-bold text-gray-800">BudgetTracker</span>
                </div>

                <div class="hidden md:flex items-center space-x-8">
                    <a href="#features"
                        class="text-gray-600 hover:text-orange-500 transition duration-300 font-medium">Features</a>
                    <a href="#how-it-works"
                        class="text-gray-600 hover:text-orange-500 transition duration-300 font-medium">How It Works</a>
                    <a href="#testimonials"
                        class="text-gray-600 hover:text-orange-500 transition duration-300 font-medium">Testimonials</a>
                </div>

                <div class="flex items-center space-x-4">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="dashboard/index.php"
                            class="bg-orange-500 hover:bg-orange-600 text-white px-6 py-2 rounded-lg font-medium transition duration-300 transform hover:scale-105">
                            Dashboard
                        </a>
                    <?php else: ?>
                        <a href="login.php" class="text-gray-600 hover:text-orange-500 font-medium transition duration-300">
                            Login
                        </a>
                        <a href="register.php"
                            class="bg-orange-500 hover:bg-orange-600 text-white px-6 py-2 rounded-lg font-medium transition duration-300 transform hover:scale-105">
                            Get Started
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="gradient-bg text-white py-20">
        <div class="container mx-auto px-6">
            <div class="flex flex-col lg:flex-row items-center justify-between">
                <div class="lg:w-1/2 mb-12 lg:mb-0 animate-fadeIn">
                    <h1 class="text-5xl lg:text-6xl font-bold mb-6 leading-tight">
                        Take Control of Your
                        <span class="text-orange-500">Financial Future</span>
                    </h1>
                    <p class="text-xl text-gray-300 mb-8 leading-relaxed">
                        BudgetTracker helps you manage your money effortlessly. Track expenses, set budgets, and achieve
                        your financial goals with our intuitive platform.
                    </p>
                    <div class="flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4">
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <a href="dashboard/index.php"
                                class="bg-orange-500 hover:bg-orange-600 text-white px-8 py-4 rounded-lg font-semibold text-lg transition duration-300 transform hover:scale-105 text-center">
                                Go to Dashboard
                            </a>
                        <?php else: ?>
                            <a href="register.php"
                                class="bg-orange-500 hover:bg-orange-600 text-white px-8 py-4 rounded-lg font-semibold text-lg transition duration-300 transform hover:scale-105 text-center">
                                Start Free Trial
                            </a>
                            <a href="#features"
                                class="border border-orange-500 text-orange-500 hover:bg-orange-500 hover:text-white px-8 py-4 rounded-lg font-semibold text-lg transition duration-300 text-center">
                                Learn More
                            </a>
                        <?php endif; ?>
                    </div>
                    <div class="mt-8 flex items-center space-x-6 text-gray-300">
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-check-circle text-orange-500"></i>
                            <span>No credit card required</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-check-circle text-orange-500"></i>
                            <span>Free forever</span>
                        </div>
                    </div>
                </div>

                <div class="lg:w-1/2 relative animate-float">
                    <div class="relative">
                        <div class="bg-white rounded-2xl shadow-2xl p-6 transform rotate-3">
                            <div class="bg-gray-800 rounded-lg p-4 mb-4">
                                <div class="flex justify-between items-center mb-4">
                                    <h3 class="text-white font-semibold">Monthly Budget</h3>
                                    <span class="text-green-400 font-bold">₦125,000</span>
                                </div>
                                <div class="space-y-3">
                                    <div>
                                        <div class="flex justify-between text-sm text-gray-300 mb-1">
                                            <span>Food & Dining</span>
                                            <span>₦45,000 / ₦50,000</span>
                                        </div>
                                        <div class="w-full bg-gray-700 rounded-full h-2">
                                            <div class="bg-green-500 h-2 rounded-full" style="width: 75%"></div>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="flex justify-between text-sm text-gray-300 mb-1">
                                            <span>Transportation</span>
                                            <span>₦15,000 / ₦20,000</span>
                                        </div>
                                        <div class="w-full bg-gray-700 rounded-full h-2">
                                            <div class="bg-orange-500 h-2 rounded-full" style="width: 75%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Floating elements -->
                        <div
                            class="absolute -top-4 -right-4 bg-orange-500 text-white p-3 rounded-xl shadow-lg animate-bounceIn">
                            <i class="fas fa-chart-pie text-xl"></i>
                        </div>
                        <div class="absolute -bottom-4 -left-4 bg-green-500 text-white p-3 rounded-xl shadow-lg animate-bounceIn"
                            style="animation-delay: 0.2s;">
                            <i class="fas fa-piggy-bank text-xl"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="bg-white py-16">
        <div class="container mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 text-center">
                <div class="animate-slideInUp">
                    <div class="stat-number text-4xl font-bold mb-2">10,000+</div>
                    <p class="text-gray-600">Active Users</p>
                </div>
                <div class="animate-slideInUp" style="animation-delay: 0.1s;">
                    <div class="stat-number text-4xl font-bold mb-2">₦5.2M+</div>
                    <p class="text-gray-600">Managed Monthly</p>
                </div>
                <div class="animate-slideInUp" style="animation-delay: 0.2s;">
                    <div class="stat-number text-4xl font-bold mb-2">98%</div>
                    <p class="text-gray-600">User Satisfaction</p>
                </div>
                <div class="animate-slideInUp" style="animation-delay: 0.3s;">
                    <div class="stat-number text-4xl font-bold mb-2">24/7</div>
                    <p class="text-gray-600">Support Available</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-20 bg-gray-50">
        <div class="container mx-auto px-6">
            <div class="text-center mb-16 animate-fadeIn">
                <h2 class="text-4xl font-bold text-gray-800 mb-4">Powerful Features for Smart Budgeting</h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">Everything you need to take control of your finances
                    in one beautiful interface</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="bg-white rounded-2xl shadow-lg p-8 card-hover animate-slideInUp">
                    <div class="feature-icon w-16 h-16 rounded-2xl flex items-center justify-center mb-6">
                        <i class="fas fa-chart-bar text-white text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-4">Smart Budget Tracking</h3>
                    <p class="text-gray-600 mb-4">Set custom budgets for different categories and track your spending in
                        real-time with beautiful visualizations.</p>
                    <ul class="space-y-2 text-gray-600">
                        <li class="flex items-center space-x-2">
                            <i class="fas fa-check text-orange-500"></i>
                            <span>Real-time expense tracking</span>
                        </li>
                        <li class="flex items-center space-x-2">
                            <i class="fas fa-check text-orange-500"></i>
                            <span>Category-wise budgeting</span>
                        </li>
                        <li class="flex items-center space-x-2">
                            <i class="fas fa-check text-orange-500"></i>
                            <span>Progress indicators</span>
                        </li>
                    </ul>
                </div>

                <!-- Feature 2 -->
                <div class="bg-white rounded-2xl shadow-lg p-8 card-hover animate-slideInUp"
                    style="animation-delay: 0.1s;">
                    <div class="feature-icon w-16 h-16 rounded-2xl flex items-center justify-center mb-6">
                        <i class="fas fa-receipt text-white text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-4">Expense Management</h3>
                    <p class="text-gray-600 mb-4">Easily add, edit, and categorize expenses. Get insights into your
                        spending patterns and habits.</p>
                    <ul class="space-y-2 text-gray-600">
                        <li class="flex items-center space-x-2">
                            <i class="fas fa-check text-orange-500"></i>
                            <span>Quick expense entry</span>
                        </li>
                        <li class="flex items-center space-x-2">
                            <i class="fas fa-check text-orange-500"></i>
                            <span>Receipt tracking</span>
                        </li>
                        <li class="flex items-center space-x-2">
                            <i class="fas fa-check text-orange-500"></i>
                            <span>Spending analytics</span>
                        </li>
                    </ul>
                </div>

                <!-- Feature 3 -->
                <div class="bg-white rounded-2xl shadow-lg p-8 card-hover animate-slideInUp"
                    style="animation-delay: 0.2s;">
                    <div class="feature-icon w-16 h-16 rounded-2xl flex items-center justify-center mb-6">
                        <i class="fas fa-chart-pie text-white text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-4">Detailed Reports</h3>
                    <p class="text-gray-600 mb-4">Generate comprehensive reports with interactive charts to understand
                        your financial health better.</p>
                    <ul class="space-y-2 text-gray-600">
                        <li class="flex items-center space-x-2">
                            <i class="fas fa-check text-orange-500"></i>
                            <span>Interactive charts</span>
                        </li>
                        <li class="flex items-center space-x-2">
                            <i class="fas fa-check text-orange-500"></i>
                            <span>Monthly comparisons</span>
                        </li>
                        <li class="flex items-center space-x-2">
                            <i class="fas fa-check text-orange-500"></i>
                            <span>Export capabilities</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section id="how-it-works" class="py-20 bg-white">
        <div class="container mx-auto px-6">
            <div class="text-center mb-16 animate-fadeIn">
                <h2 class="text-4xl font-bold text-gray-800 mb-4">How BudgetTracker Works</h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">Get started in minutes and transform your financial
                    management</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="text-center animate-slideInUp">
                    <div class="w-20 h-20 bg-orange-500 rounded-full flex items-center justify-center mx-auto mb-6">
                        <span class="text-white text-2xl font-bold">1</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-4">Sign Up & Set Budgets</h3>
                    <p class="text-gray-600">Create your account and set up your budget categories with custom limits.
                    </p>
                </div>

                <div class="text-center animate-slideInUp" style="animation-delay: 0.2s;">
                    <div class="w-20 h-20 bg-orange-500 rounded-full flex items-center justify-center mx-auto mb-6">
                        <span class="text-white text-2xl font-bold">2</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-4">Track Expenses</h3>
                    <p class="text-gray-600">Add your expenses as they happen and categorize them for better tracking.
                    </p>
                </div>

                <div class="text-center animate-slideInUp" style="animation-delay: 0.4s;">
                    <div class="w-20 h-20 bg-orange-500 rounded-full flex items-center justify-center mx-auto mb-6">
                        <span class="text-white text-2xl font-bold">3</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-4">Analyze & Improve</h3>
                    <p class="text-gray-600">Use our insights and reports to make better financial decisions.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section id="testimonials" class="py-20 bg-gray-900 text-white">
        <div class="container mx-auto px-6">
            <div class="text-center mb-16 animate-fadeIn">
                <h2 class="text-4xl font-bold mb-4">What Our Users Say</h2>
                <p class="text-xl text-gray-300 max-w-2xl mx-auto">Join thousands of satisfied users who transformed
                    their financial lives</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-gray-800 rounded-2xl p-8 card-hover animate-slideInUp">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-orange-500 rounded-full flex items-center justify-center">
                            <span class="text-white font-bold">A</span>
                        </div>
                        <div class="ml-4">
                            <h4 class="font-bold">Adeola Johnson</h4>
                            <p class="text-gray-400 text-sm">Small Business Owner</p>
                        </div>
                    </div>
                    <p class="text-gray-300">"BudgetTracker helped me save 30% more each month. The expense
                        categorization is a game-changer!"</p>
                    <div class="flex text-orange-500 mt-4">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                </div>

                <div class="bg-gray-800 rounded-2xl p-8 card-hover animate-slideInUp" style="animation-delay: 0.1s;">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-orange-500 rounded-full flex items-center justify-center">
                            <span class="text-white font-bold">C</span>
                        </div>
                        <div class="ml-4">
                            <h4 class="font-bold">Chinedu Okoro</h4>
                            <p class="text-gray-400 text-sm">Freelancer</p>
                        </div>
                    </div>
                    <p class="text-gray-300">"As a freelancer, tracking irregular income was challenging. BudgetTracker
                        made it simple and effective."</p>
                    <div class="flex text-orange-500 mt-4">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                </div>

                <div class="bg-gray-800 rounded-2xl p-8 card-hover animate-slideInUp" style="animation-delay: 0.2s;">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-orange-500 rounded-full flex items-center justify-center">
                            <span class="text-white font-bold">F</span>
                        </div>
                        <div class="ml-4">
                            <h4 class="font-bold">Funke Adebayo</h4>
                            <p class="text-gray-400 text-sm">Student</p>
                        </div>
                    </div>
                    <p class="text-gray-300">"Perfect for students! I can now manage my allowance and save for important
                        things."</p>
                    <div class="flex text-orange-500 mt-4">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 hero-gradient text-white">
        <div class="container mx-auto px-6 text-center">
            <h2 class="text-4xl font-bold mb-6 animate-fadeIn">Ready to Transform Your Finances?</h2>
            <p class="text-xl mb-8 max-w-2xl mx-auto animate-fadeIn">Join thousands of users who have taken control of
                their financial future with BudgetTracker</p>
            <div class="animate-bounceIn">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="dashboard/index.php"
                        class="bg-white text-orange-500 hover:bg-gray-100 px-8 py-4 rounded-lg font-semibold text-lg transition duration-300 transform hover:scale-105 inline-block">
                        Go to Dashboard
                    </a>
                <?php else: ?>
                    <a href="register.php"
                        class="bg-white text-orange-500 hover:bg-gray-100 px-8 py-4 rounded-lg font-semibold text-lg transition duration-300 transform hover:scale-105 inline-block">
                        Start Your Free Journey
                    </a>
                <?php endif; ?>
            </div>
            <p class="mt-4 text-orange-100 animate-fadeIn">No credit card required • Free forever • Setup in 2 minutes
            </p>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-12">
        <div class="container mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <div class="flex items-center space-x-2 mb-4">
                        <div class="w-8 h-8 bg-orange-500 rounded-lg flex items-center justify-center">
                            <i class="fas fa-wallet text-white"></i>
                        </div>
                        <span class="text-xl font-bold">BudgetTracker</span>
                    </div>
                    <p class="text-gray-400">Taking control of your financial future, one budget at a time.</p>
                </div>

                <div>
                    <h4 class="font-bold mb-4">Product</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#features" class="hover:text-orange-500 transition duration-300">Features</a></li>
                        <li><a href="#how-it-works" class="hover:text-orange-500 transition duration-300">How It
                                Works</a></li>
                        <li><a href="#testimonials"
                                class="hover:text-orange-500 transition duration-300">Testimonials</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-bold mb-4">Support</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#" class="hover:text-orange-500 transition duration-300">Help Center</a></li>
                        <li><a href="#" class="hover:text-orange-500 transition duration-300">Contact Us</a></li>
                        <li><a href="#" class="hover:text-orange-500 transition duration-300">Privacy Policy</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-bold mb-4">Connect</h4>
                    <div class="flex space-x-4">
                        <a href="#"
                            class="w-10 h-10 bg-gray-700 rounded-full flex items-center justify-center hover:bg-orange-500 transition duration-300">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#"
                            class="w-10 h-10 bg-gray-700 rounded-full flex items-center justify-center hover:bg-orange-500 transition duration-300">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#"
                            class="w-10 h-10 bg-gray-700 rounded-full flex items-center justify-center hover:bg-orange-500 transition duration-300">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-700 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; 2025 BudgetTracker. All rights reserved. Designed By ❤️ Kefas.</p>
            </div>
        </div>
    </footer>

    <script>
        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Intersection Observer for animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.animationPlayState = 'running';
                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);

        // Observe all animated elements
        document.querySelectorAll('.animate-fadeIn, .animate-slideInUp, .animate-bounceIn').forEach(el => {
            el.style.animationPlayState = 'paused';
            observer.observe(el);
        });

        // Add scroll effect to navbar
        window.addEventListener('scroll', () => {
            const nav = document.querySelector('nav');
            if (window.scrollY > 100) {
                nav.classList.add('shadow-xl');
            } else {
                nav.classList.remove('shadow-xl');
            }
        });
    </script>
</body>

</html>