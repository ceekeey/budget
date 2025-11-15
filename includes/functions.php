<?php
// Check if functions are already declared before defining them
if (!function_exists('sanitize')) {
    function sanitize($data)
    {
        if (!empty($data)) {
            return htmlspecialchars(strip_tags(trim($data)));
        }
        return $data;
    }
}

if (!function_exists('isLoggedIn')) {
    function isLoggedIn()
    {
        return isset($_SESSION['user_id']);
    }
}

if (!function_exists('redirect')) {
    function redirect($url)
    {
        header("Location: $url");
        exit();
    }
}

if (!function_exists('getBudgetSummary')) {
    function getBudgetSummary($pdo, $user_id)
    {
        $sql = "SELECT 
            bc.category_type,
            bc.category_name,
            bc.budget_limit,
            COALESCE(SUM(e.amount), 0) as total_spent,
            (bc.budget_limit - COALESCE(SUM(e.amount), 0)) as remaining
        FROM budget_categories bc
        LEFT JOIN expenses e ON bc.id = e.category_id AND MONTH(e.expense_date) = MONTH(CURRENT_DATE())
        WHERE bc.user_id = ?
        GROUP BY bc.id, bc.category_type, bc.category_name, bc.budget_limit";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

if (!function_exists('getBudgetStats')) {
    function getBudgetStats($pdo, $user_id)
    {
        $stmt = $pdo->prepare("
            SELECT 
                COUNT(*) as total_categories,
                COALESCE(SUM(budget_limit), 0) as total_budget
            FROM budget_categories 
            WHERE user_id = ?
        ");
        $stmt->execute([$user_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

if (!function_exists('getTotalExpenses')) {
    function getTotalExpenses($pdo, $user_id)
    {
        $stmt = $pdo->prepare("
            SELECT COALESCE(SUM(amount), 0) as total 
            FROM expenses 
            WHERE user_id = ? 
            AND MONTH(expense_date) = MONTH(CURRENT_DATE()) 
            AND YEAR(expense_date) = YEAR(CURRENT_DATE())
        ");
        $stmt->execute([$user_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }
}
?>