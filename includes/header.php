<?php
// Expects: $pdo, $pageTitle (string), $activePage (string)
// auth.php and functions.php already included by the calling page
$currentUser = getCurrentUser($pdo);
$flash = getFlash();

// If user session exists but account row is missing, sign them out cleanly
if (!$currentUser && isset($_SESSION['user_id'])) {
    session_destroy();
    header('Location: ' . BASE_URL . '/index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> &mdash; <?= APP_NAME ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/main.css">
</head>
<body class="bg-slate-100 min-h-screen">
<div class="flex min-h-screen">

    <!-- ── Sidebar ─────────────────────────────────── -->
    <aside class="w-64 bg-blue-900 text-white flex flex-col fixed top-0 left-0 h-full z-20 shadow-xl">

        <!-- Brand -->
        <div class="px-6 py-5 border-b border-blue-800">
            <div class="flex items-center gap-2">
                <span class="text-3xl">🏦</span>
                <div>
                    <h1 class="text-lg font-bold leading-tight"><?= APP_NAME ?></h1>
                    <p class="text-blue-400 text-xs">Personal Banking</p>
                </div>
            </div>
        </div>

        <!-- Balance Snapshot -->
        <div class="px-6 py-4 bg-blue-950 border-b border-blue-800">
            <p class="text-xs text-blue-400 mb-0.5">Account No.</p>
            <p class="text-sm font-mono font-semibold tracking-wide"><?= htmlspecialchars($currentUser['account_number'] ?? '—') ?></p>
            <p class="text-xl font-bold text-green-400 mt-1">$<?= number_format((float)($currentUser['balance'] ?? 0), 2) ?></p>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 px-4 py-4 space-y-0.5">
            <?php
            $navItems = [
                ['id' => 'dashboard', 'label' => 'Dashboard', 'icon' => '📊', 'href' => BASE_URL . '/pages/dashboard.php'],
                ['id' => 'deposit',   'label' => 'Deposit',   'icon' => '💰', 'href' => BASE_URL . '/pages/deposit.php'],
                ['id' => 'withdraw',  'label' => 'Withdraw',  'icon' => '💸', 'href' => BASE_URL . '/pages/withdraw.php'],
                ['id' => 'transfer',  'label' => 'Transfer',  'icon' => '🔄', 'href' => BASE_URL . '/pages/transfer.php'],
                ['id' => 'history',   'label' => 'History',   'icon' => '📋', 'href' => BASE_URL . '/pages/history.php'],
                ['id' => 'profile',   'label' => 'Profile',   'icon' => '👤', 'href' => BASE_URL . '/pages/profile.php'],
            ];
            foreach ($navItems as $item):
                $isActive = ($activePage === $item['id']);
            ?>
            <a href="<?= $item['href'] ?>"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors
                      <?= $isActive
                            ? 'bg-blue-700 text-white font-semibold'
                            : 'text-blue-200 hover:bg-blue-800 hover:text-white' ?>">
                <span class="text-base w-6 text-center"><?= $item['icon'] ?></span>
                <?= $item['label'] ?>
            </a>
            <?php endforeach; ?>
        </nav>

        <!-- User + Sign Out -->
        <div class="px-4 py-4 border-t border-blue-800">
            <div class="px-3 mb-2">
                <p class="text-sm font-medium text-white truncate"><?= htmlspecialchars($currentUser['name'] ?? '') ?></p>
                <p class="text-xs text-blue-400 truncate"><?= htmlspecialchars($currentUser['email'] ?? '') ?></p>
            </div>
            <a href="<?= BASE_URL ?>/logout.php"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-blue-200
                      hover:bg-red-600 hover:text-white transition-colors">
                <span class="text-base w-6 text-center">🚪</span>
                Sign Out
            </a>
        </div>
    </aside>
    <!-- ── End Sidebar ──────────────────────────────── -->

    <!-- ── Main Area ────────────────────────────────── -->
    <div class="ml-64 flex-1 flex flex-col min-h-screen">

        <!-- Top Bar -->
        <header class="bg-white border-b border-slate-200 px-8 py-4 flex items-center justify-between sticky top-0 z-10">
            <h2 class="text-xl font-semibold text-slate-700"><?= htmlspecialchars($pageTitle) ?></h2>
            <span class="text-sm text-slate-400"><?= date('l, F j, Y') ?></span>
        </header>

        <!-- Flash Message -->
        <?php if ($flash): ?>
        <div class="flash-message mx-8 mt-5 px-4 py-3 rounded-lg text-sm font-medium border
             <?= $flash['type'] === 'success'
                 ? 'bg-green-50 text-green-800 border-green-200'
                 : 'bg-red-50 text-red-800 border-red-200' ?>">
            <?= htmlspecialchars($flash['message']) ?>
        </div>
        <?php endif; ?>

        <!-- Page Content -->
        <main class="flex-1 px-8 py-6">
