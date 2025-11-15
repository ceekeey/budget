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
    <title>Expenses - Budget Tracker</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
    <?php include '../includes/sidebar.php'; ?>
    <?php include '../includes/header.php'; ?>

    <main class="ml-64 p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Expenses</h1>
            <button onclick="showAddModal()"
                class="bg-orange-500 hover:bg-orange-600 text-white px-6 py-3 rounded-lg font-medium transition duration-200">
                + Add Expense
            </button>
        </div>

        <!-- Success/Error Messages -->
        <?php if (isset($_GET['success'])): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                <?php echo htmlspecialchars($_GET['success']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?php echo htmlspecialchars($_GET['error']); ?>
            </div>
        <?php endif; ?>

        <!-- Expenses Table -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full table-auto">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-6 py-4 text-left text-sm font-medium text-gray-700">Date</th>
                            <th class="px-6 py-4 text-left text-sm font-medium text-gray-700">Category</th>
                            <th class="px-6 py-4 text-left text-sm font-medium text-gray-700">Description</th>
                            <th class="px-6 py-4 text-right text-sm font-medium text-gray-700">Amount</th>
                            <th class="px-6 py-4 text-left text-sm font-medium text-gray-700">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
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

                        if (empty($expenses)) {
                            echo '<tr><td colspan="5" class="px-6 py-8 text-center text-gray-600">
                                <div class="flex flex-col items-center">
                                    <span class="text-4xl mb-2">💸</span>
                                    <p class="text-lg">No expenses found</p>
                                    <p class="text-sm text-gray-500 mt-1">Start tracking your expenses by adding your first one!</p>
                                </div>
                            </td></tr>';
                        } else {
                            $total_amount = 0;
                            foreach ($expenses as $expense) {
                                $total_amount += $expense['amount'];
                                echo "<tr class='border-b hover:bg-gray-50'>
                                    <td class='px-6 py-4 whitespace-nowrap'>" . date('M j, Y', strtotime($expense['expense_date'])) . "</td>
                                    <td class='px-6 py-4'>
                                        <span class='bg-orange-100 text-orange-600 px-2 py-1 rounded text-sm whitespace-nowrap'>{$expense['category_name']}</span>
                                    </td>
                                    <td class='px-6 py-4 max-w-xs truncate' title='{$expense['description']}'>{$expense['description']}</td>
                                    <td class='px-6 py-4 text-right font-medium text-red-600 whitespace-nowrap'>₦" . number_format($expense['amount'], 2) . "</td>
                                    <td class='px-6 py-4 whitespace-nowrap'>
                                        <button onclick='showEditModal(" . json_encode($expense) . ")' 
                                                class='text-blue-500 hover:text-blue-700 mr-3 transition duration-200'>
                                            Edit
                                        </button>
                                        <button onclick='confirmDelete({$expense['id']}, \"₦" . number_format($expense['amount'], 2) . "\", \"" . date('M j, Y', strtotime($expense['expense_date'])) . "\")' 
                                                class='text-red-500 hover:text-red-700 transition duration-200'>
                                            Delete
                                        </button>
                                    </td>
                                </tr>";
                            }

                            // Total row
                            echo "<tr class='bg-gray-50 font-semibold'>
                                <td colspan='3' class='px-6 py-4 text-right'>Total:</td>
                                <td class='px-6 py-4 text-right text-red-600'>₦" . number_format($total_amount, 2) . "</td>
                                <td class='px-6 py-4'></td>
                            </tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Add Expense Modal -->
        <div id="addExpenseModal"
            class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-xl shadow-2xl p-6 w-96 max-h-[90vh] overflow-y-auto">
                <h3 class="text-xl font-bold text-gray-800 mb-4">Add New Expense</h3>
                <form method="POST">
                    <input type="hidden" name="add_expense" value="1">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                            <select name="category_id" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                                <option value="">Select Category</option>
                                <?php
                                $stmt = $pdo->prepare("SELECT * FROM budget_categories WHERE user_id = ? ORDER BY category_name");
                                $stmt->execute([$_SESSION['user_id']]);
                                $categories = $stmt->fetchAll();
                                foreach ($categories as $category) {
                                    echo "<option value='{$category['id']}'>{$category['category_name']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Amount</label>
                            <input type="number" name="amount" step="0.01" min="0.01" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500"
                                placeholder="0.00">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                            <input type="text" name="description"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500"
                                placeholder="Enter description (optional)">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                            <input type="date" name="expense_date" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500"
                                value="<?php echo date('Y-m-d'); ?>">
                        </div>
                    </div>
                    <div class="flex space-x-3 mt-6">
                        <button type="submit"
                            class="flex-1 bg-orange-500 hover:bg-orange-600 text-white py-2 px-4 rounded-lg font-medium transition duration-200">
                            Add Expense
                        </button>
                        <button type="button" onclick="hideAddModal()"
                            class="flex-1 bg-gray-500 hover:bg-gray-600 text-white py-2 px-4 rounded-lg font-medium transition duration-200">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Edit Expense Modal -->
        <div id="editExpenseModal"
            class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-xl shadow-2xl p-6 w-96 max-h-[90vh] overflow-y-auto">
                <h3 class="text-xl font-bold text-gray-800 mb-4">Edit Expense</h3>
                <form method="POST">
                    <input type="hidden" name="edit_expense" value="1">
                    <input type="hidden" name="expense_id" id="edit_expense_id">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                            <select name="category_id" id="edit_category_id" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                                <option value="">Select Category</option>
                                <?php
                                $stmt = $pdo->prepare("SELECT * FROM budget_categories WHERE user_id = ? ORDER BY category_name");
                                $stmt->execute([$_SESSION['user_id']]);
                                $categories = $stmt->fetchAll();
                                foreach ($categories as $category) {
                                    echo "<option value='{$category['id']}'>{$category['category_name']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Amount</label>
                            <input type="number" name="amount" id="edit_amount" step="0.01" min="0.01" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                            <input type="text" name="description" id="edit_description"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                            <input type="date" name="expense_date" id="edit_expense_date" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                        </div>
                    </div>
                    <div class="flex space-x-3 mt-6">
                        <button type="submit"
                            class="flex-1 bg-orange-500 hover:bg-orange-600 text-white py-2 px-4 rounded-lg font-medium transition duration-200">
                            Update Expense
                        </button>
                        <button type="button" onclick="hideEditModal()"
                            class="flex-1 bg-gray-500 hover:bg-gray-600 text-white py-2 px-4 rounded-lg font-medium transition duration-200">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div id="deleteModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-xl shadow-2xl p-6 w-96">
                <h3 class="text-xl font-bold text-gray-800 mb-4">Confirm Delete</h3>
                <p class="text-gray-600 mb-4" id="deleteMessage">Are you sure you want to delete this expense?</p>
                <div class="flex space-x-3">
                    <a href="#" id="deleteConfirmLink"
                        class="flex-1 bg-red-500 hover:bg-red-600 text-white py-2 px-4 rounded-lg font-medium text-center transition duration-200">
                        Delete
                    </a>
                    <button type="button" onclick="hideDeleteModal()"
                        class="flex-1 bg-gray-500 hover:bg-gray-600 text-white py-2 px-4 rounded-lg font-medium transition duration-200">
                        Cancel
                    </button>
                </div>
            </div>
        </div>

        <script>
            // Modal functions
            function showAddModal() {
                document.getElementById('addExpenseModal').classList.remove('hidden');
            }

            function hideAddModal() {
                document.getElementById('addExpenseModal').classList.add('hidden');
            }

            function showEditModal(expense) {
                document.getElementById('edit_expense_id').value = expense.id;
                document.getElementById('edit_category_id').value = expense.category_id;
                document.getElementById('edit_amount').value = expense.amount;
                document.getElementById('edit_description').value = expense.description || '';
                document.getElementById('edit_expense_date').value = expense.expense_date;
                document.getElementById('editExpenseModal').classList.remove('hidden');
            }

            function hideEditModal() {
                document.getElementById('editExpenseModal').classList.add('hidden');
            }

            function confirmDelete(expenseId, amount, date) {
                document.getElementById('deleteMessage').textContent =
                    'Are you sure you want to delete the expense of ' + amount + ' from ' + date + '? This action cannot be undone.';

                document.getElementById('deleteConfirmLink').href =
                    'expenses.php?delete_id=' + expenseId;

                document.getElementById('deleteModal').classList.remove('hidden');
            }

            function hideDeleteModal() {
                document.getElementById('deleteModal').classList.add('hidden');
            }

            // Close modals when clicking outside
            document.addEventListener('click', function (event) {
                const addModal = document.getElementById('addExpenseModal');
                const editModal = document.getElementById('editExpenseModal');
                const deleteModal = document.getElementById('deleteModal');

                if (event.target === addModal) hideAddModal();
                if (event.target === editModal) hideEditModal();
                if (event.target === deleteModal) hideDeleteModal();
            });

            // Show success/error messages and auto-hide after 5 seconds
            document.addEventListener('DOMContentLoaded', function () {
                const successMessage = document.querySelector('.bg-green-100');
                const errorMessage = document.querySelector('.bg-red-100');

                if (successMessage) {
                    setTimeout(() => {
                        successMessage.style.display = 'none';
                    }, 5000);
                }

                if (errorMessage) {
                    setTimeout(() => {
                        errorMessage.style.display = 'none';
                    }, 5000);
                }

                // Set today's date as default for add form
                document.querySelector('input[name="expense_date"]').value = new Date().toISOString().split('T')[0];
            });
        </script>
    </main>
</body>

</html>