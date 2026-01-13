<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once './../includes/auth.php';

// Debugging removed for cleaner UI, but kept in comments
/*
echo "";
echo "";
*/
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Budget Tracker</title>
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
                    fontFamily: { 'sans': ['Plus Jakarta Sans', 'sans-serif'] }
                }
            }
        }
    </script>
    <style>
        .glass { background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.1); }
        .text-gradient { background: linear-gradient(135deg, #2DD4BF 0%, #5BC0BE 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .bg-mesh { background-image: radial-gradient(at 0% 0%, rgba(45, 212, 191, 0.08) 0, transparent 50%), radial-gradient(at 100% 100%, rgba(5, 8, 17, 1) 0, transparent 50%); }
    </style>
</head>

<body class="bg-navy-950 text-gray-200 antialiased bg-mesh min-h-screen">
    <?php include '../includes/sidebar.php'; ?>
    <?php include '../includes/header.php'; ?>

    <main class="ml-64 p-8">
        <div class="mb-8">
            <h1 class="text-3xl font-extrabold text-white">Financial <span class="text-teal-500">Overview</span></h1>
            <p class="text-gray-400 text-sm">Welcome back! Here's what's happening with your money.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
            <?php
            $budget_stats = getBudgetStats($pdo, $_SESSION['user_id']);
            $total_expenses = getTotalExpenses($pdo, $_SESSION['user_id']);
            $budget_summary = getBudgetSummary($pdo, $_SESSION['user_id']);

            $total_budget = $budget_stats['total_budget'] ?? 0;
            $total_categories = $budget_stats['total_categories'] ?? 0;
            $remaining_budget = $total_budget - $total_expenses;
            ?>

            <div class="glass p-6 rounded-[2rem] border-l-4 border-teal-500 relative overflow-hidden">
                <div class="flex justify-between items-start relative z-10">
                    <div>
                        <p class="text-gray-500 text-xs uppercase font-bold tracking-wider mb-1">Total Budget</p>
                        <h3 class="text-2xl font-bold text-white">₦<?php echo number_format($total_budget, 2); ?></h3>
                    </div>
                    <div class="bg-teal-500/10 p-3 rounded-xl">
                        <i class="fas fa-wallet text-teal-500 text-lg"></i>
                    </div>
                </div>
            </div>

            <div class="glass p-6 rounded-[2rem] border-l-4 border-red-500">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-gray-500 text-xs uppercase font-bold tracking-wider mb-1">Monthly Spent</p>
                        <h3 class="text-2xl font-bold text-white">₦<?php echo number_format($total_expenses, 2); ?></h3>
                    </div>
                    <div class="bg-red-500/10 p-3 rounded-xl">
                        <i class="fas fa-arrow-trend-up text-red-500 text-lg"></i>
                    </div>
                </div>
            </div>

            <div class="glass p-6 rounded-[2rem] border-l-4 border-blue-500">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-gray-500 text-xs uppercase font-bold tracking-wider mb-1">Categories</p>
                        <h3 class="text-2xl font-bold text-white"><?php echo $total_categories; ?></h3>
                    </div>
                    <div class="bg-blue-500/10 p-3 rounded-xl">
                        <i class="fas fa-layer-group text-blue-500 text-lg"></i>
                    </div>
                </div>
            </div>

            <div class="glass p-6 rounded-[2rem] border-l-4 border-purple-500">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-gray-500 text-xs uppercase font-bold tracking-wider mb-1">Remaining</p>
                        <h3 class="text-2xl font-bold text-white">₦<?php echo number_format($remaining_budget, 2); ?></h3>
                    </div>
                    <div class="bg-purple-500/10 p-3 rounded-xl">
                        <i class="fas fa-bullseye text-purple-500 text-lg"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 glass p-8 rounded-[2.5rem]">
                <h2 class="text-xl font-bold text-white mb-6 flex items-center gap-2">
                    <i class="fas fa-chart-line text-teal-500"></i> Budget Progress
                </h2>
                <div class="space-y-6">
                    <?php
                    if (!empty($budget_summary)) {
                        foreach ($budget_summary as $budget):
                            $percentage = $budget['budget_limit'] > 0 ? ($budget['total_spent'] / $budget['budget_limit']) * 100 : 0;
                            $progress_color = $percentage > 90 ? 'bg-red-500' : ($percentage > 70 ? 'bg-yellow-500' : 'bg-teal-500');
                            ?>
                            <div>
                                <div class="flex justify-between mb-2">
                                    <span class="font-medium text-gray-300"><?php echo $budget['category_name']; ?></span>
                                    <span class="text-sm font-bold text-teal-500"><?php echo round($percentage); ?>%</span>
                                </div>
                                <div class="w-full bg-navy-800 rounded-full h-3 border border-white/5">
                                    <div class="<?php echo $progress_color; ?> h-full rounded-full transition-all duration-500"
                                         style="width: <?php echo min($percentage, 100); ?>%"></div>
                                </div>
                                <div class="flex justify-between mt-1">
                                    <span class="text-[10px] text-gray-500 uppercase">Spent: ₦<?php echo number_format($budget['total_spent']); ?></span>
                                    <span class="text-[10px] text-gray-500 uppercase">Limit: ₦<?php echo number_format($budget['budget_limit']); ?></span>
                                </div>
                            </div>
                        <?php endforeach;
                    } else {
                        echo '<div class="text-center py-10"><p class="text-gray-500 mb-4">No budgets set up yet.</p>
                              <a href="budget-categories.php" class="text-teal-500 font-bold hover:underline">Start Budgeting →</a></div>';
                    }
                    ?>
                </div>
            </div>

            <div class="glass p-8 rounded-[2.5rem]">
                <h2 class="text-xl font-bold text-white mb-6">Quick Actions</h2>
                <div class="grid grid-cols-1 gap-4">
                    <a href="expenses.php" class="flex items-center gap-4 bg-teal-500 hover:bg-teal-400 text-navy-950 p-4 rounded-2xl font-bold transition transform hover:scale-[1.02]">
                        <div class="w-10 h-10 bg-navy-950/20 rounded-lg flex items-center justify-center"><i class="fas fa-plus"></i></div>
                        Add New Expense
                    </a>
                    <a href="budget-categories.php" class="flex items-center gap-4 bg-navy-800 hover:bg-navy-700 text-white p-4 rounded-2xl border border-white/10 transition transform hover:scale-[1.02]">
                        <div class="w-10 h-10 bg-white/10 rounded-lg flex items-center justify-center text-teal-500"><i class="fas fa-folder-plus"></i></div>
                        New Category
                    </a>
                    <a href="reports.php" class="flex items-center gap-4 bg-navy-800 hover:bg-navy-700 text-white p-4 rounded-2xl border border-white/10 transition transform hover:scale-[1.02]">
                        <div class="w-10 h-10 bg-white/10 rounded-lg flex items-center justify-center text-teal-500"><i class="fas fa-file-invoice"></i></div>
                        View Reports
                    </a>
                </div>
            </div>

            <div class="lg:col-span-3 glass p-8 rounded-[2.5rem]">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-bold text-white">Recent Transactions</h2>
                    <a href="expenses.php" class="text-sm text-teal-500 font-bold hover:underline">View All</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="border-b border-white/10 text-gray-500 text-xs uppercase tracking-widest">
                                <th class="px-4 py-4 font-medium">Category</th>
                                <th class="px-4 py-4 font-medium">Description</th>
                                <th class="px-4 py-4 font-medium text-right">Amount</th>
                                <th class="px-4 py-4 font-medium text-right">Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
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
                                    echo "<tr class='hover:bg-white/5 transition group'>
                                        <td class='px-4 py-5'>
                                            <span class='bg-navy-800 text-teal-500 px-3 py-1 rounded-full text-xs font-bold border border-white/5'>{$expense['category_name']}</span>
                                        </td>
                                        <td class='px-4 py-5 text-gray-300 text-sm'>{$expense['description']}</td>
                                        <td class='px-4 py-5 text-right font-bold text-white'>₦" . number_format($expense['amount'], 2) . "</td>
                                        <td class='px-4 py-5 text-right text-gray-500 text-sm'>" . date('M j, Y', strtotime($expense['expense_date'])) . "</td>
                                    </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='4' class='px-4 py-10 text-center text-gray-500 italic'>No transactions yet. Click 'Add New Expense' to begin.</td></tr>";
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