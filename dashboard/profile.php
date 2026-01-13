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
<html lang="en" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Settings - Budget Tracker</title>
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
        .bg-mesh { background-image: radial-gradient(at 0% 0%, rgba(45, 212, 191, 0.08) 0, transparent 50%), radial-gradient(at 100% 100%, rgba(5, 8, 17, 1) 0, transparent 50%); }
        input:focus { border-color: #2DD4BF !important; box-shadow: 0 0 0 2px rgba(45, 212, 191, 0.2); }
    </style>
</head>

<body class="bg-navy-950 text-gray-200 antialiased bg-mesh min-h-screen">
    <?php include '../includes/sidebar.php'; ?>
    <?php include '../includes/header.php'; ?>

    <main class="ml-64 p-8">
        <div class="max-w-2xl mx-auto">
            <div class="mb-8">
                <h1 class="text-3xl font-extrabold text-white">Profile <span class="text-teal-500">Settings</span></h1>
                <p class="text-gray-400 text-sm">Manage your account information and preferences.</p>
            </div>

            <?php if (isset($success)): ?>
                <div class="bg-teal-500/10 border border-teal-500/50 text-teal-500 px-6 py-4 rounded-2xl mb-6 flex items-center gap-3">
                    <i class="fas fa-check-circle"></i>
                    <span class="font-medium"><?php echo $success; ?></span>
                </div>
            <?php endif; ?>

            <?php if (isset($error)): ?>
                <div class="bg-red-500/10 border border-red-500/50 text-red-500 px-6 py-4 rounded-2xl mb-6 flex items-center gap-3">
                    <i class="fas fa-exclamation-circle"></i>
                    <span class="font-medium"><?php echo $error; ?></span>
                </div>
            <?php endif; ?>

            <div class="glass rounded-[2.5rem] p-8 md:p-10 relative overflow-hidden">
                <div class="relative z-10">
                    <form method="POST">
                        <div class="space-y-6">
                            <div>
                                <label class="block text-xs uppercase tracking-widest font-bold text-gray-500 mb-2 ml-1">Full Name</label>
                                <div class="relative">
                                    <i class="fas fa-user absolute left-4 top-1/2 -translate-y-1/2 text-gray-500"></i>
                                    <input type="text" name="full_name" value="<?php echo $user['full_name']; ?>"
                                        class="w-full bg-navy-900/50 border border-white/10 rounded-2xl py-3 pl-12 pr-4 text-white focus:outline-none transition-all">
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs uppercase tracking-widest font-bold text-gray-500 mb-2 ml-1">Username</label>
                                <div class="relative">
                                    <i class="fas fa-at absolute left-4 top-1/2 -translate-y-1/2 text-gray-500"></i>
                                    <input type="text" value="<?php echo $user['username']; ?>"
                                        class="w-full bg-navy-800/30 border border-white/5 rounded-2xl py-3 pl-12 pr-4 text-gray-500 cursor-not-allowed" disabled>
                                </div>
                                <p class="text-[10px] text-gray-600 mt-2 ml-1 uppercase font-bold tracking-tighter italic">Username is fixed and cannot be changed</p>
                            </div>

                            <div>
                                <label class="block text-xs uppercase tracking-widest font-bold text-gray-500 mb-2 ml-1">Email Address</label>
                                <div class="relative">
                                    <i class="fas fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-gray-500"></i>
                                    <input type="email" name="email" value="<?php echo $user['email']; ?>" required
                                        class="w-full bg-navy-900/50 border border-white/10 rounded-2xl py-3 pl-12 pr-4 text-white focus:outline-none transition-all">
                                </div>
                            </div>

                            <div class="pt-4 border-t border-white/5 flex items-center justify-between">
                                <span class="text-xs text-gray-500 uppercase font-bold tracking-widest">Account Status</span>
                                <span class="text-sm text-gray-300">Member since <?php echo date('M Y', strtotime($user['created_at'])); ?></span>
                            </div>
                        </div>

                        <button type="submit"
                            class="w-full bg-teal-500 hover:bg-teal-400 text-navy-950 font-extrabold py-4 px-6 rounded-2xl mt-8 transition-all transform hover:scale-[1.01] active:scale-95 flex items-center justify-center gap-2">
                            <i class="fas fa-save"></i>
                            Update Profile
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </main>
</body>

</html>