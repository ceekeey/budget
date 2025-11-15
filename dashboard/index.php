<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once './../includes/auth.php';
// Make sure functions are included

// Debug: Check if we're getting here
echo "<!-- Debug: Script started -->";

// Debug: Check session
echo "<!-- Debug: User ID: " . ($_SESSION['user_id'] ?? 'Not set') . " -->";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Budget Tracker</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        orange: {
                            500: '#FF6B35',
                            600: '#E55A2B',
                        }
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-gray-100">
    <?php include '../includes/sidebar.php'; ?>
    <?php include '../includes/header.php'; ?>

    <main class="ml-64 p-6">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <?php
            // Get budget stats
            $budget_stats = getBudgetStats($pdo, $_SESSION['user_id']);
            $total_expenses = getTotalExpenses($pdo, $_SESSION['user_id']);
            $budget_summary = getBudgetSummary($pdo, $_SESSION['user_id']);

            // Calculate remaining budget safely
            $total_budget = $budget_stats['total_budget'] ?? 0;
            $total_categories = $budget_stats['total_categories'] ?? 0;
            $remaining_budget = $total_budget - $total_expenses;
            ?>

            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-orange-500">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-gray-600 text-sm">Total Budget</p>
                        <h3 class="text-2xl font-bold text-gray-800">
                            ₦<?php echo number_format($total_budget, 2); ?></h3>
                    </div>
                    <div class="bg-orange-100 p-3 rounded-full">
                        <span class="text-orange-500 text-xl">💰</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-green-500">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-gray-600 text-sm">Monthly Spent</p>
                        <h3 class="text-2xl font-bold text-gray-800">₦<?php echo number_format($total_expenses, 2); ?>
                        </h3>
                    </div>
                    <div class="bg-green-100 p-3 rounded-full">
                        <span class="text-green-500 text-xl">💸</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-blue-500">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-gray-600 text-sm">Categories</p>
                        <h3 class="text-2xl font-bold text-gray-800"><?php echo $total_categories; ?>
                        </h3>
                    </div>
                    <div class="bg-blue-100 p-3 rounded-full">
                        <span class="text-blue-500 text-xl">📁</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-purple-500">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-gray-600 text-sm">Remaining</p>
                        <h3 class="text-2xl font-bold text-gray-800">
                            ₦<?php echo number_format($remaining_budget, 2); ?></h3>
                    </div>
                    <div class="bg-purple-100 p-3 rounded-full">
                        <span class="text-purple-500 text-xl">🎯</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Budget Overview -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Budget Overview</h2>
                <div class="space-y-4">
                    <?php
                    if (!empty($budget_summary)) {
                        foreach ($budget_summary as $budget):
                            $percentage = $budget['budget_limit'] > 0 ? ($budget['total_spent'] / $budget['budget_limit']) * 100 : 0;
                            $progress_color = $percentage > 90 ? 'bg-red-500' : ($percentage > 70 ? 'bg-yellow-500' : 'bg-green-500');
                            ?>
                            <div>
                                <div class="flex justify-between mb-1">
                                    <span class="font-medium"><?php echo $budget['category_name']; ?></span>
                                    <span class="text-sm text-gray-600">₦<?php echo number_format($budget['total_spent'], 2); ?>
                                        / ₦<?php echo number_format($budget['budget_limit'], 2); ?></span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="<?php echo $progress_color; ?> h-2 rounded-full"
                                        style="width: <?php echo min($percentage, 100); ?>%"></div>
                                </div>
                            </div>
                        <?php endforeach;
                    } else {
                        echo '<p class="text-gray-600 text-center py-4">No budget categories found. <a href="budget-categories.php" class="text-orange-500 hover:underline">Create your first category</a></p>';
                    }
                    ?>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Quick Actions</h2>
                <div class="grid grid-cols-2 gap-4">
                    <a href="budget-categories.php"
                        class="bg-orange-500 hover:bg-orange-600 text-white p-4 rounded-lg text-center transition duration-200">
                        <span class="block text-2xl mb-2">📁</span>
                        <span class="font-medium">Add Category</span>
                    </a>
                    <a href="expenses.php"
                        class="bg-gray-800 hover:bg-black text-white p-4 rounded-lg text-center transition duration-200">
                        <span class="block text-2xl mb-2">💸</span>
                        <span class="font-medium">Add Expense</span>
                    </a>
                    <a href="reports.php"
                        class="bg-gray-800 hover:bg-black text-white p-4 rounded-lg text-center transition duration-200">
                        <span class="block text-2xl mb-2">📈</span>
                        <span class="font-medium">View Reports</span>
                    </a>
                    <a href="profile.php"
                        class="bg-orange-500 hover:bg-orange-600 text-white p-4 rounded-lg text-center transition duration-200">
                        <span class="block text-2xl mb-2">👤</span>
                        <span class="font-medium">Profile</span>
                    </a>
                </div>
            </div>

            <!-- Recent Expenses -->
            <div class="bg-white rounded-xl shadow-lg p-6 lg:col-span-2">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Recent Expenses</h2>
                <div class="overflow-x-auto">
                    <table class="w-full table-auto">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Category</th>
                                <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Description</th>
                                <th class="px-4 py-3 text-right text-sm font-medium text-gray-700">Amount</th>
                                <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $stmt = $pdo->prepare("
                                SELECT e.*, bc.category_name 
                                FROM expenses e 
                                JOIN budget_categories bc ON e.category_id = bc.id 
                                WHERE e.user_id = ? 
                                ORDER BY e.expense_date DESC 
                                LIMIT 5
                            ");
                            $stmt->execute([$_SESSION['user_id']]);
                            $recent_expenses = $stmt->fetchAll();

                            if ($recent_expenses) {
                                foreach ($recent_expenses as $expense) {
                                    echo "<tr class='border-b hover:bg-gray-50'>
                                        <td class='px-4 py-3'>{$expense['category_name']}</td>
                                        <td class='px-4 py-3'>{$expense['description']}</td>
                                        <td class='px-4 py-3 text-right font-medium text-red-600'>₦" . number_format($expense['amount'], 2) . "</td>
                                        <td class='px-4 py-3'>" . date('M j, Y', strtotime($expense['expense_date'])) . "</td>
                                    </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='4' class='px-4 py-3 text-center text-gray-600'>No recent expenses found. <a href='expenses.php' class='text-orange-500 hover:underline'>Add your first expense</a></td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</body>

</html>