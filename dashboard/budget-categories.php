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
    <title>Categories | Budget Tracker</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        navy: { 700: '#1e293b', 800: '#0f172a', 900: '#020617', 950: '#020617' }
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-navy-900 text-white min-h-screen">
    <?php include '../includes/sidebar.php'; ?>
    <?php include '../includes/header.php'; ?>

    <main class="ml-64 p-8">
        <div class="flex justify-between items-end mb-10">
            <div>
                <h1 class="text-4xl font-extrabold text-white tracking-tight">Budget Categories</h1>
                <p class="text-gray-400 mt-2">Manage your spending limits and track allocations.</p>
            </div>
            <button onclick="showAddModal()"
                class="bg-teal-500 hover:bg-teal-400 text-navy-950 px-6 py-3 rounded-2xl font-bold shadow-lg shadow-teal-500/20 transition-all duration-300 transform hover:-translate-y-1">
                <i class="fas fa-plus mr-2"></i> New Category
            </button>
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div class="bg-teal-500/10 border border-teal-500/20 text-teal-400 px-6 py-4 rounded-2xl mb-8 flex items-center">
                <i class="fas fa-check-circle mr-3"></i>
                <?php echo htmlspecialchars($_GET['success']); ?>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php
            $stmt = $pdo->prepare("SELECT * FROM budget_categories WHERE user_id = ? ORDER BY created_at DESC");
            $stmt->execute([$_SESSION['user_id']]);
            $categories = $stmt->fetchAll();

            if (empty($categories)): ?>
                <div class="col-span-full border-2 border-dashed border-white/10 rounded-3xl py-20 text-center">
                    <div class="bg-white/5 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-folder-open text-gray-500 text-2xl"></i>
                    </div>
                    <p class="text-gray-400 text-lg">No categories found. Start by creating one!</p>
                </div>
            <?php endif;

            foreach ($categories as $category):
                // Calculate Spending
                $stmt = $pdo->prepare("SELECT COALESCE(SUM(amount), 0) as spent FROM expenses WHERE category_id = ? AND MONTH(expense_date) = MONTH(CURRENT_DATE())");
                $stmt->execute([$category['id']]);
                $spent = $stmt->fetch()['spent'];
                
                $limit = $category['budget_limit'];
                $remaining = $limit - $spent;
                $percentage = $limit > 0 ? ($spent / $limit) * 100 : 0;
                $barColor = $percentage > 90 ? 'bg-red-500' : ($percentage > 70 ? 'bg-yellow-500' : 'bg-teal-500');

                // Expense check for delete
                $stmt = $pdo->prepare("SELECT COUNT(*) as expense_count FROM expenses WHERE category_id = ?");
                $stmt->execute([$category['id']]);
                $can_delete = $stmt->fetch()['expense_count'] == 0;
            ?>
                <div class="bg-white/5 border border-white/10 rounded-3xl p-6 hover:bg-white/[0.07] transition-all duration-300 group">
                    <div class="flex justify-between items-start mb-6">
                        <div class="bg-teal-500/10 p-3 rounded-2xl text-teal-500 group-hover:scale-110 transition-transform">
                            <i class="fas fa-tag"></i>
                        </div>
                        <div class="text-right">
                            <p class="text-xs text-gray-500 uppercase font-bold tracking-widest">Limit</p>
                            <p class="text-xl font-black text-white">₦<?php echo number_format($limit); ?></p>
                        </div>
                    </div>

                    <h3 class="text-lg font-bold text-white mb-1"><?php echo $category['category_name']; ?></h3>
                    <p class="text-xs text-gray-500 uppercase tracking-tighter mb-6"><?php echo $category['category_type']; ?></p>

                    <div class="mb-6">
                        <div class="flex justify-between text-xs mb-2">
                            <span class="text-gray-400">Spent: <b class="text-white font-medium">₦<?php echo number_format($spent); ?></b></span>
                            <span class="<?php echo $remaining < 0 ? 'text-red-400' : 'text-gray-400'; ?>">
                                <?php echo $remaining < 0 ? 'Over by' : 'Left'; ?>: <b>₦<?php echo number_format(abs($remaining)); ?></b>
                            </span>
                        </div>
                        <div class="w-full bg-navy-800 rounded-full h-2.5 overflow-hidden">
                            <div class="<?php echo $barColor; ?> h-full transition-all duration-500 shadow-[0_0_10px_rgba(20,184,166,0.3)]" 
                                 style="width: <?php echo min($percentage, 100); ?>%"></div>
                        </div>
                    </div>

                    <div class="flex space-x-3 mt-4">
                        <button onclick='showEditModal(<?php echo json_encode($category); ?>)' 
                            class="flex-1 bg-white/5 hover:bg-white/10 text-white py-3 rounded-xl text-xs font-bold transition">
                            EDIT
                        </button>
                        <button onclick='confirmDelete(<?php echo $category['id']; ?>, "<?php echo $category['category_name']; ?>", <?php echo $can_delete ? 'true' : 'false'; ?>)'
                            class="flex-1 <?php echo $can_delete ? 'bg-red-500/10 text-red-500 hover:bg-red-500 hover:text-white' : 'bg-gray-800 text-gray-600 cursor-not-allowed'; ?> py-3 rounded-xl text-xs font-bold transition">
                            DELETE
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div id="categoryModal" class="hidden fixed inset-0 bg-navy-950/80 backdrop-blur-sm flex items-center justify-center z-[60]">
            <div class="bg-navy-900 border border-white/10 rounded-[2rem] shadow-2xl p-8 w-full max-w-md transform transition-all">
                <div class="flex justify-between items-center mb-8">
                    <h3 id="modalTitle" class="text-2xl font-bold text-white tracking-tight">Add Category</h3>
                    <button onclick="hideModal()" class="text-gray-500 hover:text-white"><i class="fas fa-times"></i></button>
                </div>
                
                <form method="POST" id="categoryForm">
                    <input type="hidden" name="add_category" id="form_action_type" value="1">
                    <input type="hidden" name="category_id" id="modal_category_id">
                    
                    <div class="space-y-6">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2 ml-1">Category Name</label>
                            <input type="text" name="category_name" id="modal_category_name" required
                                class="w-full bg-navy-800 border border-white/5 rounded-2xl px-5 py-4 text-white focus:outline-none focus:ring-2 focus:ring-teal-500/50 transition">
                        </div>
                        
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2 ml-1">Type</label>
                            <select name="category_type" id="modal_category_type" required
                                class="w-full bg-navy-800 border border-white/5 rounded-2xl px-5 py-4 text-white focus:outline-none focus:ring-2 focus:ring-teal-500/50 appearance-none transition">
                                <option value="travel">Travel</option>
                                <option value="financial">Financial</option>
                                <option value="spending">Spending</option>
                                <option value="other">Other</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2 ml-1">Budget Limit (₦)</label>
                            <input type="number" name="budget_limit" id="modal_budget_limit" step="0.01" min="0" required
                                class="w-full bg-navy-800 border border-white/5 rounded-2xl px-5 py-4 text-white focus:outline-none focus:ring-2 focus:ring-teal-500/50 transition font-mono">
                        </div>
                    </div>

                    <button type="submit" id="submitBtn"
                        class="w-full mt-10 bg-teal-500 hover:bg-teal-400 text-navy-950 py-4 rounded-2xl font-black tracking-widest transition-all shadow-lg shadow-teal-500/10">
                        SAVE CATEGORY
                    </button>
                </form>
            </div>
        </div>

        <div id="deleteModal" class="hidden fixed inset-0 bg-navy-950/80 backdrop-blur-sm flex items-center justify-center z-[70]">
             <div class="bg-navy-900 border border-red-500/20 rounded-[2rem] p-8 w-96 text-center">
                <div class="w-20 h-20 bg-red-500/10 text-red-500 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-exclamation-triangle text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold text-white mb-2 tracking-tight">Are you sure?</h3>
                <p class="text-gray-400 text-sm mb-8" id="deleteMessage"></p>
                <div class="flex space-x-3">
                    <button onclick="hideDeleteModal()" class="flex-1 bg-white/5 text-white py-3 rounded-xl font-bold">CANCEL</button>
                    <a href="#" id="deleteConfirmLink" class="flex-1 bg-red-500 text-white py-3 rounded-xl font-bold hover:bg-red-600 transition">DELETE</a>
                </div>
             </div>
        </div>

        <script>
            function showAddModal() {
                document.getElementById('modalTitle').innerText = "Add New Category";
                document.getElementById('form_action_type').name = "add_category";
                document.getElementById('submitBtn').innerText = "CREATE CATEGORY";
                document.getElementById('categoryForm').reset();
                document.getElementById('categoryModal').classList.remove('hidden');
            }

            function showEditModal(category) {
                document.getElementById('modalTitle').innerText = "Edit Category";
                document.getElementById('form_action_type').name = "edit_category";
                document.getElementById('modal_category_id').value = category.id;
                document.getElementById('modal_category_name').value = category.category_name;
                document.getElementById('modal_category_type').value = category.category_type;
                document.getElementById('modal_budget_limit').value = category.budget_limit;
                document.getElementById('submitBtn').innerText = "UPDATE CATEGORY";
                document.getElementById('categoryModal').classList.remove('hidden');
            }

            function hideModal() {
                document.getElementById('categoryModal').classList.add('hidden');
            }

            function confirmDelete(id, name, canDelete) {
                if (!canDelete) {
                    alert('Cannot delete category "' + name + '" because it has associated expenses.');
                    return;
                }
                document.getElementById('deleteMessage').innerText = "This will permanently remove the " + name + " category.";
                document.getElementById('deleteConfirmLink').href = 'budget-categories.php?delete_id=' + id;
                document.getElementById('deleteModal').classList.remove('hidden');
            }

            function hideDeleteModal() {
                document.getElementById('deleteModal').classList.add('hidden');
            }
        </script>
    </main>
</body>
</html>