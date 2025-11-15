<?php
require_once '../includes/auth.php';

// Get current month and year
$current_month = date('F Y');
$current_year = date('Y');

// Get monthly summary data
$stmt = $pdo->prepare("
    SELECT 
        bc.category_name,
        bc.category_type,
        bc.budget_limit,
        COALESCE(SUM(e.amount), 0) as spent,
        (bc.budget_limit - COALESCE(SUM(e.amount), 0)) as remaining
    FROM budget_categories bc
    LEFT JOIN expenses e ON bc.id = e.category_id AND MONTH(e.expense_date) = MONTH(CURRENT_DATE()) AND YEAR(e.expense_date) = YEAR(CURRENT_DATE())
    WHERE bc.user_id = ?
    GROUP BY bc.id
    ORDER BY spent DESC
");
$stmt->execute([$_SESSION['user_id']]);
$monthly_data = $stmt->fetchAll();

// Get expense by category for current month
$stmt = $pdo->prepare("
    SELECT 
        bc.category_name,
        bc.category_type,
        SUM(e.amount) as total_spent
    FROM expenses e
    JOIN budget_categories bc ON e.category_id = bc.id
    WHERE e.user_id = ? AND MONTH(e.expense_date) = MONTH(CURRENT_DATE()) AND YEAR(e.expense_date) = YEAR(CURRENT_DATE())
    GROUP BY bc.id
    ORDER BY total_spent DESC
");
$stmt->execute([$_SESSION['user_id']]);
$category_expenses = $stmt->fetchAll();

// Get monthly trends (last 6 months)
$stmt = $pdo->prepare("
    SELECT 
        DATE_FORMAT(expense_date, '%Y-%m') as month,
        SUM(amount) as total_spent,
        COUNT(*) as expense_count
    FROM expenses 
    WHERE user_id = ? AND expense_date >= DATE_SUB(CURRENT_DATE, INTERVAL 6 MONTH)
    GROUP BY DATE_FORMAT(expense_date, '%Y-%m')
    ORDER BY month DESC
    LIMIT 6
");
$stmt->execute([$_SESSION['user_id']]);
$monthly_trends = $stmt->fetchAll();

// Get top expenses
$stmt = $pdo->prepare("
    SELECT 
        e.amount,
        e.description,
        e.expense_date,
        bc.category_name
    FROM expenses e
    JOIN budget_categories bc ON e.category_id = bc.id
    WHERE e.user_id = ? AND MONTH(e.expense_date) = MONTH(CURRENT_DATE())
    ORDER BY e.amount DESC
    LIMIT 5
");
$stmt->execute([$_SESSION['user_id']]);
$top_expenses = $stmt->fetchAll();

// Calculate totals
$total_budget = array_sum(array_column($monthly_data, 'budget_limit'));
$total_spent = array_sum(array_column($monthly_data, 'spent'));
$total_remaining = $total_budget - $total_spent;
$savings_rate = $total_budget > 0 ? (($total_budget - $total_spent) / $total_budget) * 100 : 0;

// Prepare data for charts
$chart_categories = [];
$chart_spent = [];
$chart_budget = [];
$chart_colors = ['#FF6B35', '#4ECDC4', '#45B7D1', '#96CEB4', '#FFEAA7', '#DDA0DD', '#98D8C8', '#F7DC6F'];

foreach ($monthly_data as $index => $data) {
    $chart_categories[] = $data['category_name'];
    $chart_spent[] = (float) $data['spent'];
    $chart_budget[] = (float) $data['budget_limit'];
}

// Monthly trends data
$trend_months = [];
$trend_amounts = [];
foreach (array_reverse($monthly_trends) as $trend) {
    $trend_months[] = date('M Y', strtotime($trend['month'] . '-01'));
    $trend_amounts[] = (float) $trend['total_spent'];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - Budget Tracker</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="bg-gray-100">
    <?php include '../includes/sidebar.php'; ?>
    <?php include '../includes/header.php'; ?>

    <main class="ml-64 p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Financial Reports</h1>
            <div class="text-sm text-gray-600 bg-white px-4 py-2 rounded-lg shadow">
                <?php echo $current_month; ?>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-blue-500">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-gray-600 text-sm">Total Budget</p>
                        <h3 class="text-2xl font-bold text-gray-800">₦<?php echo number_format($total_budget, 2); ?>
                        </h3>
                    </div>
                    <div class="bg-blue-100 p-3 rounded-full">
                        <span class="text-blue-500 text-xl">💰</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-red-500">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-gray-600 text-sm">Total Spent</p>
                        <h3 class="text-2xl font-bold text-gray-800">₦<?php echo number_format($total_spent, 2); ?></h3>
                    </div>
                    <div class="bg-red-100 p-3 rounded-full">
                        <span class="text-red-500 text-xl">💸</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-green-500">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-gray-600 text-sm">Remaining</p>
                        <h3 class="text-2xl font-bold text-gray-800">₦<?php echo number_format($total_remaining, 2); ?>
                        </h3>
                    </div>
                    <div class="bg-green-100 p-3 rounded-full">
                        <span class="text-green-500 text-xl">🎯</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-purple-500">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-gray-600 text-sm">Savings Rate</p>
                        <h3 class="text-2xl font-bold text-gray-800"><?php echo number_format($savings_rate, 1); ?>%
                        </h3>
                    </div>
                    <div class="bg-purple-100 p-3 rounded-full">
                        <span class="text-purple-500 text-xl">📈</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Budget vs Actual Chart -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Budget vs Actual Spending</h2>
                <div class="h-80">
                    <canvas id="budgetChart"></canvas>
                </div>
            </div>

            <!-- Expense Distribution Chart -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Expense Distribution</h2>
                <div class="h-80">
                    <canvas id="expenseChart"></canvas>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Monthly Trends Chart -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Spending Trends (Last 6 Months)</h2>
                <div class="h-80">
                    <canvas id="trendChart"></canvas>
                </div>
            </div>

            <!-- Category Performance -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Category Performance</h2>
                <div class="space-y-4">
                    <?php foreach ($monthly_data as $data):
                        $usage_percentage = $data['budget_limit'] > 0 ? ($data['spent'] / $data['budget_limit']) * 100 : 0;
                        $status_color = $usage_percentage > 90 ? 'bg-red-500' : ($usage_percentage > 70 ? 'bg-yellow-500' : 'bg-green-500');
                        $status_text = $usage_percentage > 90 ? 'Over Budget' : ($usage_percentage > 70 ? 'Close to Limit' : 'Within Budget');
                        ?>
                        <div class="p-4 border rounded-lg">
                            <div class="flex justify-between items-center mb-2">
                                <span class="font-medium"><?php echo $data['category_name']; ?></span>
                                <span
                                    class="text-sm <?php echo $usage_percentage > 90 ? 'text-red-600' : ($usage_percentage > 70 ? 'text-yellow-600' : 'text-green-600'); ?>">
                                    <?php echo $status_text; ?>
                                </span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2 mb-2">
                                <div class="<?php echo $status_color; ?> h-2 rounded-full"
                                    style="width: <?php echo min($usage_percentage, 100); ?>%"></div>
                            </div>
                            <div class="flex justify-between text-sm text-gray-600">
                                <span>Spent: ₦<?php echo number_format($data['spent'], 2); ?></span>
                                <span>Budget: ₦<?php echo number_format($data['budget_limit'], 2); ?></span>
                                <span><?php echo number_format($usage_percentage, 1); ?>%</span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Top Expenses -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Top 5 Expenses This Month</h2>
                <div class="space-y-3">
                    <?php if (!empty($top_expenses)): ?>
                        <?php foreach ($top_expenses as $expense): ?>
                            <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                                <div>
                                    <span class="font-medium"><?php echo $expense['category_name']; ?></span>
                                    <p class="text-sm text-gray-600"><?php echo $expense['description'] ?: 'No description'; ?>
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        <?php echo date('M j', strtotime($expense['expense_date'])); ?>
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="font-medium text-red-600">₦<?php echo number_format($expense['amount'], 2); ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-gray-600 text-center py-4">No expenses this month</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Quick Stats</h2>
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Average Daily Spend</span>
                        <span class="font-medium">₦<?php echo number_format($total_spent / date('t'), 2); ?></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Days Remaining in Month</span>
                        <span class="font-medium"><?php echo date('t') - date('j'); ?></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Projected Month End</span>
                        <span
                            class="font-medium <?php echo ($total_spent / date('j') * date('t')) > $total_budget ? 'text-red-600' : 'text-green-600'; ?>">
                            ₦<?php echo number_format($total_spent / date('j') * date('t'), 2); ?>
                        </span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Expenses Count</span>
                        <span class="font-medium"><?php echo count($category_expenses); ?></span>
                    </div>
                </div>
            </div>
        </div>

        <script>
            // Budget vs Actual Chart
            const budgetCtx = document.getElementById('budgetChart').getContext('2d');
            const budgetChart = new Chart(budgetCtx, {
                type: 'bar',
                data: {
                    labels: <?php echo json_encode($chart_categories); ?>,
                    datasets: [{
                        label: 'Budget',
                        data: <?php echo json_encode($chart_budget); ?>,
                        backgroundColor: '#4ECDC4',
                        borderColor: '#45B7D1',
                        borderWidth: 1
                    }, {
                        label: 'Actual Spending',
                        data: <?php echo json_encode($chart_spent); ?>,
                        backgroundColor: '#FF6B35',
                        borderColor: '#E55A2B',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function (value) {
                                    return '₦' + value.toLocaleString();
                                }
                            }
                        }
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function (context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    label += '₦' + context.parsed.y.toLocaleString();
                                    return label;
                                }
                            }
                        }
                    }
                }
            });

            // Expense Distribution Chart
            const expenseCtx = document.getElementById('expenseChart').getContext('2d');
            const expenseChart = new Chart(expenseCtx, {
                type: 'doughnut',
                data: {
                    labels: <?php echo json_encode($chart_categories); ?>,
                    datasets: [{
                        data: <?php echo json_encode($chart_spent); ?>,
                        backgroundColor: <?php echo json_encode(array_slice($chart_colors, 0, count($chart_categories))); ?>,
                        borderWidth: 2,
                        borderColor: '#ffffff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: {
                                boxWidth: 12,
                                padding: 15
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function (context) {
                                    const label = context.label || '';
                                    const value = context.parsed;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = Math.round((value / total) * 100);
                                    return `${label}: ₦${value.toLocaleString()} (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });

            // Monthly Trends Chart
            const trendCtx = document.getElementById('trendChart').getContext('2d');
            const trendChart = new Chart(trendCtx, {
                type: 'line',
                data: {
                    labels: <?php echo json_encode($trend_months); ?>,
                    datasets: [{
                        label: 'Monthly Spending',
                        data: <?php echo json_encode($trend_amounts); ?>,
                        backgroundColor: 'rgba(255, 107, 53, 0.1)',
                        borderColor: '#FF6B35',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#FF6B35',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 5
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function (value) {
                                    return '₦' + value.toLocaleString();
                                }
                            }
                        }
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function (context) {
                                    return 'Spent: ₦' + context.parsed.y.toLocaleString();
                                }
                            }
                        }
                    }
                }
            });

            // Add animation to summary cards
            document.addEventListener('DOMContentLoaded', function () {
                const cards = document.querySelectorAll('.bg-white.rounded-xl');
                cards.forEach((card, index) => {
                    card.style.opacity = '0';
                    card.style.transform = 'translateY(20px)';
                    setTimeout(() => {
                        card.style.transition = 'all 0.6s ease';
                        card.style.opacity = '1';
                        card.style.transform = 'translateY(0)';
                    }, index * 100);
                });
            });
        </script>
    </main>
</body>

</html>