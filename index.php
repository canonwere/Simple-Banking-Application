<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

// Already logged in — go to dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . '/pages/dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email']    ?? '');
    $password =      $_POST['password'] ?? '';

    if ($email && $password) {
        $stmt = $pdo->prepare("SELECT id, name, password_hash FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            setFlash('success', 'Welcome back, ' . $user['name'] . '!');
            header('Location: ' . BASE_URL . '/pages/dashboard.php');
            exit;
        }
    }
    $error = 'Invalid email or password. Please try again.';
}

// Show flash (e.g. "Account created" from register)
$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In &mdash; <?= APP_NAME ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/main.css">
</head>
<body class="min-h-screen bg-gradient-to-br from-blue-900 via-blue-800 to-blue-700
             flex items-center justify-center p-4">

    <div class="auth-card">
        <!-- Logo -->
        <div class="text-center mb-8">
            <span class="text-6xl">🏦</span>
            <h1 class="text-3xl font-bold text-slate-800 mt-3"><?= APP_NAME ?></h1>
            <p class="text-slate-500 mt-1">Sign in to your account</p>
        </div>

        <?php if ($flash): ?>
        <div class="mb-4 px-4 py-3 rounded-lg text-sm font-medium border
            <?= $flash['type'] === 'success' ? 'bg-green-50 text-green-800 border-green-200' : 'bg-red-50 text-red-800 border-red-200' ?>">
            <?= htmlspecialchars($flash['message']) ?>
        </div>
        <?php endif; ?>

        <?php if ($error): ?>
        <div class="mb-4 px-4 py-3 rounded-lg text-sm font-medium bg-red-50 text-red-800 border border-red-200">
            <?= htmlspecialchars($error) ?>
        </div>
        <?php endif; ?>

        <form method="POST" novalidate>
            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-1">Email Address</label>
                <input type="email" name="email" class="form-input"
                       placeholder="you@example.com"
                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                       required autofocus>
            </div>
            <div class="mb-6">
                <label class="block text-sm font-medium text-slate-700 mb-1">Password</label>
                <input type="password" name="password" class="form-input"
                       placeholder="Your password" required>
            </div>
            <button type="submit" class="btn-primary">Sign In</button>
        </form>

        <p class="text-center text-sm text-slate-500 mt-6">
            Don't have an account?
            <a href="<?= BASE_URL ?>/register.php" class="text-blue-600 font-semibold hover:underline">Open one free</a>
        </p>
    </div>

    <script src="<?= BASE_URL ?>/assets/js/main.js"></script>
</body>
</html>
