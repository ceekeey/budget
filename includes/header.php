<nav class="bg-white shadow-lg">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center py-4">
            <a href="index.php" class="text-xl font-bold text-gray-800">Budget Tracker</a>

            <div class="flex items-center space-x-4">
                <a href="index.php" class="text-gray-600 hover:text-gray-800">Home</a>
                <?php if (isLoggedIn()): ?>
                    <a href="dashboard.php" class="text-gray-600 hover:text-gray-800">Dashboard</a>
                    <a href="profile.php" class="text-gray-600 hover:text-gray-800">Profile</a>
                    <a href="logout.php" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="text-gray-600 hover:text-gray-800">Login</a>
                    <a href="register.php" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>