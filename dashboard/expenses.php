<?php
require_once '../includes/auth.php';

// Handle Add Expense
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_expense'])) {
    $category_id = $_POST['category_id'];
    $amount = $_POST['amount'];
    $description = sanitize($_POST['description']);
    $expense_date = $_POST['expense_date'];

    // Verify the category belongs to the current user
    $stmt = $pdo->prepare("SELECT id FROM budget_categories WHERE id = ? AND user_id = ?");
    $stmt->execute([$category_id, $_SESSION['user_id']]);

    if ($stmt->rowCount() > 0) {
        $stmt = $pdo->prepare("INSERT INTO expenses (user_id, category_id, amount, description, expense_date) VALUES (?, ?, ?, ?, ?)");
        if ($stmt->execute([$_SESSION['user_id'], $category_id, $amount, $description, $expense_date])) {
            header('Location: expenses.php?success=Expense added successfully');
            exit();
        } else {
            header('Location: expenses.php?error=Failed to add expense');
            exit();
        }
    } else {
        header('Location: expenses.php?error=Invalid category selected');
        exit();
    }
}

// Handle Edit Expense
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_expense'])) {
    $expense_id = $_POST['expense_id'];
    $category_id = $_POST['category_id'];
    $amount = $_POST['amount'];
    $description = sanitize($_POST['description']);
    $expense_date = $_POST['expense_date'];

    // Verify the expense belongs to the current user
    $stmt = $pdo->prepare("SELECT id FROM expenses WHERE id = ? AND user_id = ?");
    $stmt->execute([$expense_id, $_SESSION['user_id']]);

    if ($stmt->rowCount() > 0) {
        // Verify the category belongs to the current user
        $stmt = $pdo->prepare("SELECT id FROM budget_categories WHERE id = ? AND user_id = ?");
        $stmt->execute([$category_id, $_SESSION['user_id']]);

        if ($stmt->rowCount() > 0) {
            $stmt = $pdo->prepare("UPDATE expenses SET category_id = ?, amount = ?, description = ?, expense_date = ? WHERE id = ?");
            if ($stmt->execute([$category_id, $amount, $description, $expense_date, $expense_id])) {
                header('Location: expenses.php?success=Expense updated successfully');
                exit();
            } else {
                header('Location: expenses.php?error=Failed to update expense');
                exit();
            }
        } else {
            header('Location: expenses.php?error=Invalid category selected');
            exit();
        }
    } else {
        header('Location: expenses.php?error=Expense not found');
        exit();
    }
}

// Handle Delete Expense
if (isset($_GET['delete_id'])) {
    $expense_id = $_GET['delete_id'];

    // Verify the expense belongs to the current user
    $stmt = $pdo->prepare("SELECT id FROM expenses WHERE id = ? AND user_id = ?");
    $stmt->execute([$expense_id, $_SESSION['user_id']]);

    if ($stmt->rowCount() > 0) {
        $stmt = $pdo->prepare("DELETE FROM expenses WHERE id = ?");
        if ($stmt->execute([$expense_id])) {
            header('Location: expenses.php?success=Expense deleted successfully');
            exit();
        } else {
            header('Location: expenses.php?error=Failed to delete expense');
            exit();
        }
    } else {
        header('Location: expenses.php?error=Expense not found');
        exit();
    }
}

// Get expense for editing
$edit_expense = null;
if (isset($_GET['edit_id'])) {
    $expense_id = $_GET['edit_id'];
    $stmt = $pdo->prepare("SELECT * FROM expenses WHERE id = ? AND user_id = ?");
    $stmt->execute([$expense_id, $_SESSION['user_id']]);
    $edit_expense = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expenses | Budget Tracker</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        navy: { 700: '#1e293b', 800: '#0f172a', 900: '#020617', 950: '#010409' }
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-navy-950 text-white min-h-screen font-sans">
    <?php include '../includes/sidebar.php'; ?>
    <?php include '../includes/header.php'; ?>

    <main class="ml-64 p-8">
        <div class="flex justify-between items-end mb-10">
            <div>
                <h1 class="text-4xl font-extrabold text-white tracking-tight">Transaction Log</h1>
                <p class="text-gray-400 mt-2">View and manage your recent spending activity.</p>
            </div>
            <button onclick="showAddModal()"
                class="bg-teal-500 hover:bg-teal-400 text-navy-950 px-6 py-3 rounded-2xl font-bold shadow-lg shadow-teal-500/20 transition-all duration-300 transform hover:-translate-y-1">
                <i class="fas fa-plus mr-2"></i> Add Expense
            </button>
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div class="bg-teal-500/10 border border-teal-500/20 text-teal-400 px-6 py-4 rounded-2xl mb-8 flex items-center">
                <i class="fas fa-check-circle mr-3"></i>
                <?php echo htmlspecialchars($_GET['success']); ?>
            </div>
        <?php endif; ?>

        <div class="bg-white/5 border border-white/10 rounded-[2.5rem] overflow-hidden backdrop-blur-md">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-white/10">
                            <th class="px-8 py-6 text-xs font-bold text-gray-500 uppercase tracking-widest">Date</th>
                            <th class="px-8 py-6 text-xs font-bold text-gray-500 uppercase tracking-widest">Category</th>
                            <th class="px-8 py-6 text-xs font-bold text-gray-500 uppercase tracking-widest">Description</th>
                            <th class="px-8 py-6 text-xs font-bold text-gray-500 uppercase tracking-widest text-right">Amount</th>
                            <th class="px-8 py-6 text-xs font-bold text-gray-500 uppercase tracking-widest text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        <?php
                        $stmt = $pdo->prepare("
                            SELECT e.*, bc.category_name 
                            FROM expenses e 
                            JOIN budget_categories bc ON e.category_id = bc.id 
                            WHERE e.user_id = ? 
                            ORDER BY e.expense_date DESC, e.created_at DESC
                        ");
                        $stmt->execute([$_SESSION['user_id']]);
                        $expenses = $stmt->fetchAll();

                        if (empty($expenses)): ?>
                            <tr>
                                <td colspan="5" class="px-8 py-20 text-center">
                                    <div class="bg-white/5 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <i class="fas fa-receipt text-gray-600 text-2xl"></i>
                                    </div>
                                    <p class="text-gray-400 text-lg">No expenses recorded yet.</p>
                                </td>
                            </tr>
                        <?php else: 
                            $total_amount = 0;
                            foreach ($expenses as $expense): 
                                $total_amount += $expense['amount'];
                        ?>
                            <tr class="hover:bg-white/[0.03] transition-colors group">
                                <td class="px-8 py-5">
                                    <span class="text-sm font-medium text-gray-300">
                                        <?php echo date('M d, Y', strtotime($expense['expense_date'])); ?>
                                    </span>
                                </td>
                                <td class="px-8 py-5">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-bold tracking-tighter bg-teal-500/10 text-teal-400 border border-teal-500/20 uppercase">
                                        <i class="fas fa-tag mr-1.5 text-[8px]"></i>
                                        <?php echo $expense['category_name']; ?>
                                    </span>
                                </td>
                                <td class="px-8 py-5">
                                    <p class="text-sm text-white font-medium truncate max-w-xs">
                                        <?php echo $expense['description'] ?: '<span class="text-gray-600 italic">No description</span>'; ?>
                                    </p>
                                </td>
                                <td class="px-8 py-5 text-right">
                                    <span class="text-base font-black text-white">
                                        ₦<?php echo number_format($expense['amount'], 2); ?>
                                    </span>
                                </td>
                                <td class="px-8 py-5">
                                    <div class="flex justify-center space-x-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <button onclick='showEditModal(<?php echo json_encode($expense); ?>)' 
                                            class="p-2 hover:bg-white/10 rounded-xl text-gray-400 hover:text-white transition">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button onclick='confirmDelete(<?php echo $expense['id']; ?>, "₦<?php echo number_format($expense['amount'], 2); ?>", "<?php echo date('M d, Y', strtotime($expense['expense_date'])); ?>")'
                                            class="p-2 hover:bg-red-500/10 rounded-xl text-gray-400 hover:text-red-500 transition">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                            <tr class="bg-white/[0.02]">
                                <td colspan="3" class="px-8 py-6 text-right text-xs font-bold text-gray-500 uppercase tracking-widest">Total Expenses</td>
                                <td class="px-8 py-6 text-right">
                                    <span class="text-xl font-black text-teal-400">₦<?php echo number_format($total_amount, 2); ?></span>
                                </td>
                                <td></td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div id="expenseModal" class="hidden fixed inset-0 bg-navy-950/80 backdrop-blur-sm flex items-center justify-center z-[60]">
            <div class="bg-navy-900 border border-white/10 rounded-[2.5rem] shadow-2xl p-8 w-full max-w-md">
                <div class="flex justify-between items-center mb-8">
                    <h3 id="modalTitle" class="text-2xl font-bold text-white tracking-tight">Add Expense</h3>
                    <button onclick="hideModal()" class="text-gray-500 hover:text-white"><i class="fas fa-times"></i></button>
                </div>
                
                <form method="POST" id="expenseForm">
                    <input type="hidden" name="add_expense" id="form_action_type" value="1">
                    <input type="hidden" name="expense_id" id="modal_expense_id">
                    
                    <div class="space-y-5">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2 ml-1">Category</label>
                            <select name="category_id" id="modal_category_id" required
                                class="w-full bg-navy-800 border border-white/5 rounded-2xl px-5 py-4 text-white focus:outline-none focus:ring-2 focus:ring-teal-500/50 appearance-none transition">
                                <option value="">Select Category</option>
                                <?php
                                $stmt = $pdo->prepare("SELECT * FROM budget_categories WHERE user_id = ? ORDER BY category_name");
                                $stmt->execute([$_SESSION['user_id']]);
                                $categories = $stmt->fetchAll();
                                foreach ($categories as $cat) {
                                    echo "<option value='{$cat['id']}'>{$cat['category_name']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2 ml-1">Amount (₦)</label>
                            <input type="number" name="amount" id="modal_amount" step="0.01" min="0.01" required
                                class="w-full bg-navy-800 border border-white/5 rounded-2xl px-5 py-4 text-white focus:outline-none focus:ring-2 focus:ring-teal-500/50 transition font-mono">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2 ml-1">Description</label>
                            <input type="text" name="description" id="modal_description" placeholder="What was this for?"
                                class="w-full bg-navy-800 border border-white/5 rounded-2xl px-5 py-4 text-white focus:outline-none focus:ring-2 focus:ring-teal-500/50 transition">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2 ml-1">Date</label>
                            <input type="date" name="expense_date" id="modal_expense_date" required
                                class="w-full bg-navy-800 border border-white/5 rounded-2xl px-5 py-4 text-white focus:outline-none focus:ring-2 focus:ring-teal-500/50 transition">
                        </div>
                    </div>

                    <button type="submit" id="submitBtn"
                        class="w-full mt-10 bg-teal-500 hover:bg-teal-400 text-navy-950 py-4 rounded-2xl font-black tracking-widest transition-all shadow-lg shadow-teal-500/10">
                        SAVE TRANSACTION
                    </button>
                </form>
            </div>
        </div>

        <div id="deleteModal" class="hidden fixed inset-0 bg-navy-950/80 backdrop-blur-sm flex items-center justify-center z-[70]">
             <div class="bg-navy-900 border border-red-500/20 rounded-[2.5rem] p-8 w-96 text-center">
                <div class="w-20 h-20 bg-red-500/10 text-red-500 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-trash-alt text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold text-white mb-2">Delete Transaction?</h3>
                <p class="text-gray-400 text-sm mb-8" id="deleteMessage"></p>
                <div class="flex space-x-3">
                    <button onclick="hideDeleteModal()" class="flex-1 bg-white/5 text-white py-3 rounded-xl font-bold">CANCEL</button>
                    <a href="#" id="deleteConfirmLink" class="flex-1 bg-red-500 text-white py-3 rounded-xl font-bold">DELETE</a>
                </div>
             </div>
        </div>

        <script>
            function showAddModal() {
                document.getElementById('modalTitle').innerText = "Add Expense";
                document.getElementById('form_action_type').name = "add_expense";
                document.getElementById('expenseForm').reset();
                document.getElementById('modal_expense_date').value = new Date().toISOString().split('T')[0];
                document.getElementById('expenseModal').classList.remove('hidden');
            }

            function showEditModal(expense) {
                document.getElementById('modalTitle').innerText = "Edit Transaction";
                document.getElementById('form_action_type').name = "edit_expense";
                document.getElementById('modal_expense_id').value = expense.id;
                document.getElementById('modal_category_id').value = expense.category_id;
                document.getElementById('modal_amount').value = expense.amount;
                document.getElementById('modal_description').value = expense.description || '';
                document.getElementById('modal_expense_date').value = expense.expense_date;
                document.getElementById('expenseModal').classList.remove('hidden');
            }

            function hideModal() { document.getElementById('expenseModal').classList.add('hidden'); }

            function confirmDelete(id, amount, date) {
                document.getElementById('deleteMessage').innerText = "Are you sure you want to delete the expense of " + amount + " from " + date + "?";
                document.getElementById('deleteConfirmLink').href = 'expenses.php?delete_id=' + id;
                document.getElementById('deleteModal').classList.remove('hidden');
            }

            function hideDeleteModal() { document.getElementById('deleteModal').classList.add('hidden'); }

            // Close modals on outside click
            window.onclick = function(event) {
                if (event.target.id == 'expenseModal') hideModal();
                if (event.target.id == 'deleteModal') hideDeleteModal();
            }
        </script>
    </main>
</body>
</html>