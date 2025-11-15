<!-- Top Header -->
<header class="bg-gray-900 text-white ml-64">
    <div class="flex justify-between items-center p-4">
        <div>
            <h1 class="text-2xl font-bold">Welcome, <?php echo $_SESSION['full_name'] ?? 'User'; ?>!</h1>
            <p class="text-orange-400">Manage your finances efficiently</p>
        </div>
        <div class="flex items-center space-x-4">
            <span class="text-orange-400"><?php echo date('F j, Y'); ?></span>
            <div class="w-10 h-10 bg-orange-500 rounded-full flex items-center justify-center">
                <span
                    class="font-bold text-black"><?php echo strtoupper(substr($_SESSION['username'] ?? 'U', 0, 1)); ?></span>
            </div>
        </div>
    </div>
</header>