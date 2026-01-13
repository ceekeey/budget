<nav class="ml-64 bg-navy-950/50 backdrop-blur-md border-b border-white/5 sticky top-0 z-40">
    <div class="px-8 py-4">
        <div class="flex justify-between items-center">
            <div class="flex items-center">
                <span class="text-gray-500 text-xs font-bold uppercase tracking-widest">Portal / </span>
                <span class="text-teal-500 text-xs font-bold uppercase tracking-widest ml-2">
                    <?php 
                    $title = str_replace(['-', '.php'], [' ', ''], basename($_SERVER['PHP_SELF']));
                    echo $title == 'index' ? 'Dashboard' : $title;
                    ?>
                </span>
            </div>

            <div class="flex items-center space-x-6">
                <button class="text-gray-500 hover:text-white transition">
                    <i class="far fa-bell text-lg"></i>
                </button>

                <div class="h-8 w-[1px] bg-white/10"></div>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="flex items-center space-x-3 group cursor-pointer">
                        <div class="text-right">
                            <p class="text-xs font-bold text-white group-hover:text-teal-500 transition">Account Active</p>
                            <p class="text-[10px] text-gray-500">ID: #<?php echo $_SESSION['user_id']; ?></p>
                        </div>
                        <div class="w-10 h-10 bg-gradient-to-tr from-teal-500 to-navy-700 rounded-full flex items-center justify-center border border-white/10 shadow-lg">
                            <i class="fas fa-user text-white text-sm"></i>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="/budget/login.php" class="text-sm font-bold text-gray-400 hover:text-white transition">Sign In</a>
                    <a href="/budget/register.php" class="bg-teal-500 text-navy-900 px-5 py-2 rounded-xl text-sm font-bold hover:bg-teal-400 transition">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>