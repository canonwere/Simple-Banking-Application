<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

if (isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . '/pages/dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name         = trim($_POST['name']            ?? '');
    $email        = trim($_POST['email']           ?? '');
    $password     =      $_POST['password']        ?? '';
    $confirm      =      $_POST['confirm_password'] ?? '';
    $account_type = in_array($_POST['account_type'] ?? '', ['savings', 'checking'])
                    ? $_POST['account_type'] : 'savings';

    if (!$name || !$email || !$password || !$confirm) {
        $error = 'All fields are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match.';
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = 'An account with that email already exists.';
        } else {
            $pdo->beginTransaction();
            try {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $pdo->prepare("INSERT INTO users (name, email, password_hash) VALUES (?, ?, ?)")
                    ->execute([$name, $email, $hash]);
                $userId = (int)$pdo->lastInsertId();

                $accNum = generateAccountNumber();
                $pdo->prepare("INSERT INTO accounts (user_id, account_number, account_type) VALUES (?, ?, ?)")
                    ->execute([$userId, $accNum, $account_type]);

                $pdo->commit();
                setFlash('success', 'Account created! Your account number is ' . $accNum . '. Please sign in.');
                header('Location: ' . BASE_URL . '/index.php');
                exit;
            } catch (Exception $e) {
                $pdo->rollBack();
                $error = 'Registration failed. Please try again.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account &mdash; <?= APP_NAME ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/main.css">
</head>
<body class="min-h-screen bg-gradient-to-br from-blue-900 via-blue-800 to-blue-700
             flex items-center justify-center p-4">

    <div class="auth-card" style="max-width:480px">
        <div class="text-center mb-8">
            <span class="text-6xl">🏦</span>
            <h1 class="text-3xl font-bold text-slate-800 mt-3">Open an Account</h1>
            <p class="text-slate-500 mt-1">Free, instant, no fees</p>
        </div>

        <?php if ($error): ?>
        <div class="mb-4 px-4 py-3 rounded-lg text-sm font-medium bg-red-50 text-red-800 border border-red-200">
            <?= htmlspecialchars($error) ?>
        </div>
        <?php endif; ?>

        <form method="POST" novalidate>
            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-1">Full Name</label>
                <input type="text" name="name" class="form-input"
                       placeholder="Jane Doe"
                       value="<?= htmlspecialchars($_POST['name'] ?? '') ?>"
                       required autofocus>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-1">Email Address</label>
                <input type="email" name="email" class="form-input"
                       placeholder="you@example.com"
                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                       required>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-1">Account Type</label>
                <select name="account_type" class="form-input">
                    <option value="savings"  <?= ($_POST['account_type'] ?? 'savings') === 'savings'  ? 'selected' : '' ?>>Savings Account</option>
                    <option value="checking" <?= ($_POST['account_type'] ?? '') === 'checking' ? 'selected' : '' ?>>Checking Account</option>
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-1">Password</label>
                <input type="password" name="password" class="form-input"
                       placeholder="At least 6 characters" required>
            </div>
            <div class="mb-6">
                <label class="block text-sm font-medium text-slate-700 mb-1">Confirm Password</label>
                <input type="password" name="confirm_password" class="form-input"
                       placeholder="Repeat password" required>
            </div>
            <button type="submit" class="btn-primary">Create Account</button>
        </form>

        <p class="text-center text-sm text-slate-500 mt-6">
            Already have an account?
            <a href="<?= BASE_URL ?>/index.php" class="text-blue-600 font-semibold hover:underline">Sign in</a>
        </p>
    </div>

    <script src="<?= BASE_URL ?>/assets/js/main.js"></script>
</body>
</html>
