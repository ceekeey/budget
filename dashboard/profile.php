<?php require_once '../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = sanitize($_POST['full_name']);
    $email = sanitize($_POST['email']);

    $stmt = $pdo->prepare("UPDATE users SET full_name = ?, email = ? WHERE id = ?");
    if ($stmt->execute([$full_name, $email, $_SESSION['user_id']])) {
        $_SESSION['full_name'] = $full_name;
        $success = "Profile updated successfully!";
    } else {
        $error = "Failed to update profile!";
    }
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Budget Tracker</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
    <?php include '../includes/sidebar.php'; ?>
    <?php include '../includes/header.php'; ?>

    <main class="ml-64 p-6">
        <div class="max-w-2xl mx-auto">
            <h1 class="text-3xl font-bold text-gray-800 mb-6">Profile Settings</h1>

            <?php if (isset($success)): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    <?php echo $success; ?>
                </div>
            <?php endif; ?>

            <?php if (isset($error)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <div class="bg-white rounded-xl shadow-lg p-6">
                <form method="POST">
                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                            <input type="text" name="full_name" value="<?php echo $user['full_name']; ?>"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Username</label>
                            <input type="text" value="<?php echo $user['username']; ?>"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100" disabled>
                            <p class="text-sm text-gray-500 mt-1">Username cannot be changed</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                            <input type="email" name="email" value="<?php echo $user['email']; ?>" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Member Since</label>
                            <p class="text-gray-600"><?php echo date('F j, Y', strtotime($user['created_at'])); ?></p>
                        </div>
                    </div>

                    <button type="submit"
                        class="w-full bg-orange-500 hover:bg-orange-600 text-white font-bold py-3 px-4 rounded-lg mt-6 transition duration-200">
                        Update Profile
                    </button>
                </form>
            </div>
        </div>
    </main>
</body>

</html>