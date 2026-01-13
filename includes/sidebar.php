<div class="bg-navy-950 text-gray-400 w-64 min-h-screen fixed left-0 top-0 overflow-y-auto border-r border-white/5 shadow-2xl z-50">
    <div class="p-8">
        <div class="flex items-center space-x-3 mb-12">
            <div class="w-10 h-10 bg-teal-500 rounded-xl flex items-center justify-center shadow-lg shadow-teal-500/20">
                <i class="fas fa-chart-pie text-navy-900 text-lg"></i>
            </div>
            <span class="text-xl font-extrabold tracking-tight text-white">Budget<span class="text-teal-500">Tracker</span></span>
        </div>

        <nav class="space-y-4">
            <?php 
            $current_page = basename($_SERVER['PHP_SELF']); 
            
            // Helper function for active classes
            function navItem($url, $icon, $label, $current_page, $target_page) {
                $isActive = ($current_page == $target_page);
                $class = $isActive 
                    ? "flex items-center space-x-3 p-3.5 rounded-2xl bg-teal-500 text-navy-950 font-bold shadow-lg shadow-teal-500/20 transition duration-300" 
                    : "flex items-center space-x-3 p-3.5 rounded-2xl hover:bg-white/5 hover:text-white transition duration-200 border border-transparent hover:border-white/10";
                
                echo "<a href='$url' class='$class'>
                        <i class='fas $icon w-5 text-center'></i>
                        <span class='text-sm tracking-wide'>$label</span>
                      </a>";
            }
            ?>

            <?php navItem('/budget/dashboard/index.php', 'fa-grip-vertical', 'Dashboard', $current_page, 'index.php'); ?>
            <?php navItem('/budget/dashboard/budget-categories.php', 'fa-layer-group', 'Categories', $current_page, 'budget-categories.php'); ?>
            <?php navItem('/budget/dashboard/expenses.php', 'fa-receipt', 'Expenses', $current_page, 'expenses.php'); ?>
            <?php navItem('/budget/dashboard/reports.php', 'fa-chart-bar', 'Reports', $current_page, 'reports.php'); ?>
            <?php navItem('/budget/dashboard/profile.php', 'fa-user-circle', 'Profile', $current_page, 'profile.php'); ?>

            <div class="pt-10">
                <a href="/budget/logout.php"
                    class="flex items-center space-x-3 p-3.5 rounded-2xl text-red-400 hover:bg-red-500/10 hover:text-red-300 transition duration-200 border border-transparent hover:border-red-500/20">
                    <i class="fas fa-sign-out-alt w-5 text-center"></i>
                    <span class="text-sm font-bold">Logout</span>
                </a>
            </div>
        </nav>
    </div>
</div>