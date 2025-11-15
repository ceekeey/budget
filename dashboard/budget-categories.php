<?php
require_once '../includes/auth.php';

// Handle Add Category
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_category'])) {
    $category_name = sanitize($_POST['category_name']);
    $category_type = sanitize($_POST['category_type']);
    $budget_limit = $_POST['budget_limit'];

    $stmt = $pdo->prepare("INSERT INTO budget_categories (user_id, category_name, category_type, budget_limit) VALUES (?, ?, ?, ?)");
    if ($stmt->execute([$_SESSION['user_id'], $category_name, $category_type, $budget_limit])) {
        header('Location: budget-categories.php?success=Category added successfully');
        exit();
    } else {
        header('Location: budget-categories.php?error=Failed to add category');
        exit();
    }
}

// Handle Edit Category
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_category'])) {
    $category_id = $_POST['category_id'];
    $category_name = sanitize($_POST['category_name']);
    $category_type = sanitize($_POST['category_type']);
    $budget_limit = $_POST['budget_limit'];

    // Verify the category belongs to the current user
    $stmt = $pdo->prepare("SELECT id FROM budget_categories WHERE id = ? AND user_id = ?");
    $stmt->execute([$category_id, $_SESSION['user_id']]);

    if ($stmt->rowCount() > 0) {
        $stmt = $pdo->prepare("UPDATE budget_categories SET category_name = ?, category_type = ?, budget_limit = ? WHERE id = ?");
        if ($stmt->execute([$category_name, $category_type, $budget_limit, $category_id])) {
            header('Location: budget-categories.php?success=Category updated successfully');
            exit();
        } else {
            header('Location: budget-categories.php?error=Failed to update category');
            exit();
        }
    } else {
        header('Location: budget-categories.php?error=Category not found');
        exit();
    }
}

// Handle Delete Category
if (isset($_GET['delete_id'])) {
    $category_id = $_GET['delete_id'];

    // Verify the category belongs to the current user
    $stmt = $pdo->prepare("SELECT id FROM budget_categories WHERE id = ? AND user_id = ?");
    $stmt->execute([$category_id, $_SESSION['user_id']]);

    if ($stmt->rowCount() > 0) {
        // Check if category has expenses
        $stmt = $pdo->prepare("SELECT COUNT(*) as expense_count FROM expenses WHERE category_id = ?");
        $stmt->execute([$category_id]);
        $expense_count = $stmt->fetch()['expense_count'];

        if ($expense_count > 0) {
            header('Location: budget-categories.php?error=Cannot delete category with existing expenses');
            exit();
        }

        $stmt = $pdo->prepare("DELETE FROM budget_categories WHERE id = ?");
        if ($stmt->execute([$category_id])) {
            header('Location: budget-categories.php?success=Category deleted successfully');
            exit();
        } else {
            header('Location: budget-categories.php?error=Failed to delete category');
            exit();
        }
    } else {
        header('Location: budget-categories.php?error=Category not found');
        exit();
    }
}

// Get category for editing
$edit_category = null;
if (isset($_GET['edit_id'])) {
    $category_id = $_GET['edit_id'];
    $stmt = $pdo->prepare("SELECT * FROM budget_categories WHERE id = ? AND user_id = ?");
    $stmt->execute([$category_id, $_SESSION['user_id']]);
    $edit_category = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Budget Categories - Budget Tracker</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
    <?php include '../includes/sidebar.php'; ?>
    <?php include '../includes/header.php'; ?>

    <main class="ml-64 p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Budget Categories</h1>
            <button onclick="showAddModal()"
                class="bg-orange-500 hover:bg-orange-600 text-white px-6 py-3 rounded-lg font-medium transition duration-200">
                + Add Category
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

        <!-- Categories Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php
            $stmt = $pdo->prepare("SELECT * FROM budget_categories WHERE user_id = ? ORDER BY created_at DESC");
            $stmt->execute([$_SESSION['user_id']]);
            $categories = $stmt->fetchAll();

            if (empty($categories)) {
                echo '<div class="col-span-3 text-center py-8">
                    <p class="text-gray-600 text-lg">No categories found. Create your first budget category to get started!</p>
                </div>';
            }

            foreach ($categories as $category) {
                $stmt = $pdo->prepare("SELECT COALESCE(SUM(amount), 0) as spent FROM expenses WHERE category_id = ? AND MONTH(expense_date) = MONTH(CURRENT_DATE())");
                $stmt->execute([$category['id']]);
                $spent = $stmt->fetch()['spent'];
                $remaining = $category['budget_limit'] - $spent;
                $percentage = $category['budget_limit'] > 0 ? ($spent / $category['budget_limit']) * 100 : 0;

                // Check if category has expenses
                $stmt = $pdo->prepare("SELECT COUNT(*) as expense_count FROM expenses WHERE category_id = ?");
                $stmt->execute([$category['id']]);
                $expense_count = $stmt->fetch()['expense_count'];
                $can_delete = $expense_count == 0;

                echo "
                <div class='bg-white rounded-xl shadow-lg p-6 border-l-4 border-orange-500'>
                    <div class='flex justify-between items-start mb-4'>
                        <div>
                            <h3 class='text-lg font-bold text-gray-800'>{$category['category_name']}</h3>
                            <span class='text-sm text-gray-600 capitalize'>{$category['category_type']}</span>
                        </div>
                        <span class='bg-orange-100 text-orange-600 px-2 py-1 rounded text-sm font-medium'>₦" . number_format($category['budget_limit'], 2) . "</span>
                    </div>
                    
                    <div class='mb-4'>
                        <div class='flex justify-between text-sm mb-1'>
                            <span>Spent: ₦" . number_format($spent, 2) . "</span>
                            <span>Remaining: ₦" . number_format($remaining, 2) . "</span>
                        </div>
                        <div class='w-full bg-gray-200 rounded-full h-2'>
                            <div class='bg-orange-500 h-2 rounded-full' style='width: " . min($percentage, 100) . "%'></div>
                        </div>
                    </div>
                    
                    <div class='flex space-x-2'>
                        <button onclick='showEditModal(" . json_encode($category) . ")' 
                                class='flex-1 bg-gray-800 hover:bg-black text-white py-2 px-3 rounded text-sm transition duration-200'>
                            Edit
                        </button>
                        <button onclick='confirmDelete({$category['id']}, \"{$category['category_name']}\", {$can_delete})' 
                                class='flex-1 bg-red-500 hover:bg-red-600 text-white py-2 px-3 rounded text-sm transition duration-200 " . ($can_delete ? '' : 'opacity-50 cursor-not-allowed') . "' 
                                " . ($can_delete ? '' : 'disabled') . ">
                            Delete
                        </button>
                    </div>
                    " . (!$can_delete ? '<p class="text-xs text-red-500 mt-2">Cannot delete - has expenses</p>' : '') . "
                </div>";
            }
            ?>
        </div>

        <!-- Add Category Modal -->
        <div id="addCategoryModal"
            class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-xl shadow-2xl p-6 w-96">
                <h3 class="text-xl font-bold text-gray-800 mb-4">Add New Category</h3>
                <form method="POST">
                    <input type="hidden" name="add_category" value="1">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Category Name</label>
                            <input type="text" name="category_name" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500"
                                placeholder="Enter category name">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Category Type</label>
                            <select name="category_type" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                                <option value="">Select Type</option>
                                <option value="travel">Travel</option>
                                <option value="financial">Financial</option>
                                <option value="spending">Spending</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Budget Limit</label>
                            <input type="number" name="budget_limit" step="0.01" min="0" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500"
                                placeholder="0.00">
                        </div>
                    </div>
                    <div class="flex space-x-3 mt-6">
                        <button type="submit"
                            class="flex-1 bg-orange-500 hover:bg-orange-600 text-white py-2 px-4 rounded-lg font-medium transition duration-200">
                            Add Category
                        </button>
                        <button type="button" onclick="hideAddModal()"
                            class="flex-1 bg-gray-500 hover:bg-gray-600 text-white py-2 px-4 rounded-lg font-medium transition duration-200">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Edit Category Modal -->
        <div id="editCategoryModal"
            class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-xl shadow-2xl p-6 w-96">
                <h3 class="text-xl font-bold text-gray-800 mb-4">Edit Category</h3>
                <form method="POST">
                    <input type="hidden" name="edit_category" value="1">
                    <input type="hidden" name="category_id" id="edit_category_id">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Category Name</label>
                            <input type="text" name="category_name" id="edit_category_name" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Category Type</label>
                            <select name="category_type" id="edit_category_type" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                                <option value="travel">Travel</option>
                                <option value="financial">Financial</option>
                                <option value="spending">Spending</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Budget Limit</label>
                            <input type="number" name="budget_limit" id="edit_budget_limit" step="0.01" min="0" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                        </div>
                    </div>
                    <div class="flex space-x-3 mt-6">
                        <button type="submit"
                            class="flex-1 bg-orange-500 hover:bg-orange-600 text-white py-2 px-4 rounded-lg font-medium transition duration-200">
                            Update Category
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
                <p class="text-gray-600 mb-4" id="deleteMessage">Are you sure you want to delete this category?</p>
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
                document.getElementById('addCategoryModal').classList.remove('hidden');
            }

            function hideAddModal() {
                document.getElementById('addCategoryModal').classList.add('hidden');
            }

            function showEditModal(category) {
                document.getElementById('edit_category_id').value = category.id;
                document.getElementById('edit_category_name').value = category.category_name;
                document.getElementById('edit_category_type').value = category.category_type;
                document.getElementById('edit_budget_limit').value = category.budget_limit;
                document.getElementById('editCategoryModal').classList.remove('hidden');
            }

            function hideEditModal() {
                document.getElementById('editCategoryModal').classList.add('hidden');
            }

            function confirmDelete(categoryId, categoryName, canDelete) {
                if (!canDelete) {
                    alert('Cannot delete category "' + categoryName + '" because it has associated expenses. Please delete the expenses first.');
                    return;
                }

                document.getElementById('deleteMessage').textContent =
                    'Are you sure you want to delete the category "' + categoryName + '"? This action cannot be undone.';

                document.getElementById('deleteConfirmLink').href =
                    'budget-categories.php?delete_id=' + categoryId;

                document.getElementById('deleteModal').classList.remove('hidden');
            }

            function hideDeleteModal() {
                document.getElementById('deleteModal').classList.add('hidden');
            }

            // Close modals when clicking outside
            document.addEventListener('click', function (event) {
                const addModal = document.getElementById('addCategoryModal');
                const editModal = document.getElementById('editCategoryModal');
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
            });
        </script>
    </main>
</body>

</html>