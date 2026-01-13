<?php
require_once '../includes/auth.php';

// ==========================================
// 1. CSV EXPORT LOGIC (Must be at the very top)
// ==========================================
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    $stmt = $pdo->prepare("
        SELECT bc.category_name, bc.budget_limit, 
               COALESCE(SUM(e.amount), 0) as spent,
               (bc.budget_limit - COALESCE(SUM(e.amount), 0)) as remaining
        FROM budget_categories bc
        LEFT JOIN expenses e ON bc.id = e.category_id 
             AND MONTH(e.expense_date) = MONTH(CURRENT_DATE()) 
             AND YEAR(e.expense_date) = YEAR(CURRENT_DATE())
        WHERE bc.user_id = ?
        GROUP BY bc.id
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $export_data = $stmt->fetchAll();

    $filename = "Budget_Report_" . date('M_Y') . ".csv";
    
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    
    $output = fopen('php://output', 'w');
    fputcsv($output, ['Category', 'Budget (N)', 'Spent (N)', 'Remaining (N)']);
    
    foreach ($export_data as $row) {
        fputcsv($output, [
            $row['category_name'], 
            $row['budget_limit'], 
            $row['spent'], 
            $row['remaining']
        ]);
    }
    fclose($output);
    exit; 
}

// ==========================================
// 2. DATA FETCHING FOR DASHBOARD
// ==========================================
$current_month = date('F Y');

// Monthly summary data
$stmt = $pdo->prepare("
    SELECT 
        bc.category_name,
        bc.budget_limit,
        COALESCE(SUM(e.amount), 0) as spent
    FROM budget_categories bc
    LEFT JOIN expenses e ON bc.id = e.category_id AND MONTH(e.expense_date) = MONTH(CURRENT_DATE()) AND YEAR(e.expense_date) = YEAR(CURRENT_DATE())
    WHERE bc.user_id = ?
    GROUP BY bc.id
    ORDER BY spent DESC
");
$stmt->execute([$_SESSION['user_id']]);
$monthly_data = $stmt->fetchAll();

// Monthly trends (last 6 months)
$stmt = $pdo->prepare("
    SELECT 
        DATE_FORMAT(expense_date, '%Y-%m') as month,
        SUM(amount) as total_spent
    FROM expenses 
    WHERE user_id = ? AND expense_date >= DATE_SUB(CURRENT_DATE, INTERVAL 6 MONTH)
    GROUP BY DATE_FORMAT(expense_date, '%Y-%m')
    ORDER BY month ASC
");
$stmt->execute([$_SESSION['user_id']]);
$monthly_trends = $stmt->fetchAll();

// Calculate totals
$total_budget = array_sum(array_column($monthly_data, 'budget_limit'));
$total_spent = array_sum(array_column($monthly_data, 'spent'));
$total_remaining = $total_budget - $total_spent;
$savings_rate = $total_budget > 0 ? (($total_budget - $total_spent) / $total_budget) * 100 : 0;

// Prepare chart data
$chart_categories = []; $chart_spent = []; $chart_budget = [];
foreach ($monthly_data as $data) {
    $chart_categories[] = $data['category_name'];
    $chart_spent[] = (float)$data['spent'];
    $chart_budget[] = (float)$data['budget_limit'];
}

$trend_months = []; $trend_amounts = [];
foreach ($monthly_trends as $trend) {
    $trend_months[] = date('M Y', strtotime($trend['month'] . '-01'));
    $trend_amounts[] = (float)$trend['total_spent'];
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        teal: { 500: '#2DD4BF' },
                        navy: { 950: '#050811', 900: '#0B132B', 800: '#1C2541' }
                    },
                    fontFamily: { 'sans': ['Plus Jakarta Sans', 'sans-serif'] }
                }
            }
        }
    </script>
    <style>
        .glass { background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.1); }
        .bg-mesh { background-image: radial-gradient(at 0% 0%, rgba(45, 212, 191, 0.08) 0, transparent 50%), radial-gradient(at 100% 100%, rgba(5, 8, 17, 1) 0, transparent 50%); }
    </style>
</head>

<body class="bg-navy-950 text-gray-200 bg-mesh min-h-screen">
    <?php include '../includes/sidebar.php'; ?>
    <?php include '../includes/header.php'; ?>

    <main class="ml-64 p-8">
        <div class="flex justify-between items-center mb-10">
            <div>
                <h1 class="text-3xl font-extrabold text-white">Financial <span class="text-teal-500">Reports</span></h1>
                <p class="text-gray-400 text-sm">Detailed analysis for <?= $current_month ?></p>
            </div>
            <div class="flex gap-3">
                <a href="?export=csv" class="glass px-5 py-2.5 rounded-xl text-sm font-bold text-white hover:bg-white/10 transition flex items-center gap-2 border-white/20">
                    <i class="fas fa-download text-teal-500"></i> Export CSV
                </a>
                <div class="glass px-5 py-2.5 rounded-xl text-sm font-bold text-teal-500">
                    <i class="far fa-calendar-alt mr-2"></i> <?= $current_month ?>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-10">
            <div class="glass p-6 rounded-[2rem] border-l-4 border-teal-500">
                <p class="text-gray-500 text-xs font-bold uppercase tracking-widest mb-1">Budget</p>
                <h3 class="text-2xl font-bold">₦<?= number_format($total_budget) ?></h3>
            </div>
            <div class="glass p-6 rounded-[2rem] border-l-4 border-red-500">
                <p class="text-gray-500 text-xs font-bold uppercase tracking-widest mb-1">Spent</p>
                <h3 class="text-2xl font-bold">₦<?= number_format($total_spent) ?></h3>
            </div>
            <div class="glass p-6 rounded-[2rem] border-l-4 border-blue-500">
                <p class="text-gray-500 text-xs font-bold uppercase tracking-widest mb-1">Remaining</p>
                <h3 class="text-2xl font-bold">₦<?= number_format($total_remaining) ?></h3>
            </div>
            <div class="glass p-6 rounded-[2rem] border-l-4 border-purple-500">
                <p class="text-gray-500 text-xs font-bold uppercase tracking-widest mb-1">Savings Rate</p>
                <h3 class="text-2xl font-bold"><?= number_format($savings_rate, 1) ?>%</h3>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-10">
            <div class="glass p-8 rounded-[2.5rem]">
                <h2 class="text-lg font-bold mb-6 flex items-center gap-2"><i class="fas fa-chart-bar text-teal-500"></i> Budget vs Actual</h2>
                <div class="h-72"><canvas id="budgetChart"></canvas></div>
            </div>
            <div class="glass p-8 rounded-[2.5rem]">
                <h2 class="text-lg font-bold mb-6 flex items-center gap-2"><i class="fas fa-chart-line text-teal-500"></i> 6-Month Trend</h2>
                <div class="h-72"><canvas id="trendChart"></canvas></div>
            </div>
        </div>

        <div class="glass p-8 rounded-[2.5rem]">
            <h2 class="text-lg font-bold mb-6">Category Utilization</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-6">
                <?php foreach ($monthly_data as $data): 
                    $usage = $data['budget_limit'] > 0 ? ($data['spent'] / $data['budget_limit']) * 100 : 0;
                    $color = $usage > 90 ? 'bg-red-500' : ($usage > 70 ? 'bg-yellow-500' : 'bg-teal-500');
                ?>
                <div>
                    <div class="flex justify-between text-sm mb-2">
                        <span class="text-gray-300"><?= $data['category_name'] ?></span>
                        <span class="font-bold"><?= number_format($usage, 1) ?>%</span>
                    </div>
                    <div class="w-full bg-navy-800 rounded-full h-2">
                        <div class="<?= $color ?> h-full rounded-full transition-all" style="width: <?= min($usage, 100) ?>%"></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <script>
            Chart.defaults.color = '#94a3b8';
            Chart.defaults.borderColor = 'rgba(255, 255, 255, 0.05)';

            // Budget Chart
            new Chart(document.getElementById('budgetChart'), {
                type: 'bar',
                data: {
                    labels: <?= json_encode($chart_categories) ?>,
                    datasets: [{
                        label: 'Budget',
                        data: <?= json_encode($chart_budget) ?>,
                        backgroundColor: '#1C2541',
                        borderRadius: 6
                    }, {
                        label: 'Actual',
                        data: <?= json_encode($chart_spent) ?>,
                        backgroundColor: '#2DD4BF',
                        borderRadius: 6
                    }]
                },
                options: { responsive: true, maintainAspectRatio: false }
            });

            // Trend Chart
            new Chart(document.getElementById('trendChart'), {
                type: 'line',
                data: {
                    labels: <?= json_encode($trend_months) ?>,
                    datasets: [{
                        label: 'Spent',
                        data: <?= json_encode($trend_amounts) ?>,
                        borderColor: '#2DD4BF',
                        backgroundColor: 'rgba(45, 212, 191, 0.1)',
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: { responsive: true, maintainAspectRatio: false }
            });
        </script>
    </main>
</body>
</html>