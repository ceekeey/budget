<!-- Sidebar -->
<div class="bg-black text-orange-500 w-64 min-h-screen fixed left-0 top-0 overflow-y-auto">
    <div class="p-6">
        <h1 class="text-2xl font-bold text-orange-500 mb-8">💰 Budget Tracker</h1>

        <nav class="space-y-2">
            <a href="/budget/dashboard/index.php"
                class="flex items-center space-x-3 p-3 rounded-lg hover:bg-orange-500 hover:text-black transition duration-200 <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'bg-orange-500 text-black' : ''; ?>">
                <span>📊</span>
                <span>Dashboard</span>
            </a>

            <a href="/budget/dashboard/budget-categories.php"
                class="flex items-center space-x-3 p-3 rounded-lg hover:bg-orange-500 hover:text-black transition duration-200 <?php echo basename($_SERVER['PHP_SELF']) == 'budget-categories.php' ? 'bg-orange-500 text-black' : ''; ?>">
                <span>📁</span>
                <span>Budget Categories</span>
            </a>

            <a href="/budget/dashboard/expenses.php"
                class="flex items-center space-x-3 p-3 rounded-lg hover:bg-orange-500 hover:text-black transition duration-200 <?php echo basename($_SERVER['PHP_SELF']) == 'expenses.php' ? 'bg-orange-500 text-black' : ''; ?>">
                <span>💸</span>
                <span>Expenses</span>
            </a>

            <a href="/budget/dashboard/reports.php"
                class="flex items-center space-x-3 p-3 rounded-lg hover:bg-orange-500 hover:text-black transition duration-200 <?php echo basename($_SERVER['PHP_SELF']) == 'reports.php' ? 'bg-orange-500 text-black' : ''; ?>">
                <span>📈</span>
                <span>Reports</span>
            </a>

            <a href="/budget/dashboard/profile.php"
                class="flex items-center space-x-3 p-3 rounded-lg hover:bg-orange-500 hover:text-black transition duration-200 <?php echo basename($_SERVER['PHP_SELF']) == 'profile.php' ? 'bg-orange-500 text-black' : ''; ?>">
                <span>👤</span>
                <span>Profile</span>
            </a>

            <a href="/budget/logout.php"
                class="flex items-center space-x-3 p-3 rounded-lg hover:bg-red-500 hover:text-white transition duration-200 mt-8">
                <span>🚪</span>
                <span>Logout</span>
            </a>
        </nav>
    </div>
</div>