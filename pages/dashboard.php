<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
requireLogin();

$pageTitle  = 'Dashboard';
$activePage = 'dashboard';
require_once __DIR__ . '/../includes/header.php';

// Stats
$stmt = $pdo->prepare("
    SELECT
        SUM(CASE WHEN type IN ('deposit','transfer_in')  THEN amount ELSE 0 END) AS total_in,
        SUM(CASE WHEN type IN ('withdrawal','transfer_out') THEN amount ELSE 0 END) AS total_out,
        COUNT(*) AS total_tx
    FROM transactions WHERE account_id = ?
");
$stmt->execute([$currentUser['account_id']]);
$stats = $stmt->fetch();

// Recent 5 transactions
$stmt = $pdo->prepare("SELECT * FROM transactions WHERE account_id = ? ORDER BY created_at DESC LIMIT 5");
$stmt->execute([$currentUser['account_id']]);
$recentTx = $stmt->fetchAll();
?>

<!-- ── Stats Cards ───────────────────────────── -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">

    <div class="stat-card bg-gradient-to-br from-blue-600 to-blue-800 text-white rounded-xl p-6 shadow-md">
        <p class="text-blue-200 text-sm font-medium mb-1">Current Balance</p>
        <p class="text-4xl font-bold">$<?= number_format((float)$currentUser['balance'], 2) ?></p>
        <p class="text-blue-300 text-xs mt-2 font-mono"><?= htmlspecialchars($currentUser['account_number']) ?></p>
        <p class="text-blue-300 text-xs capitalize"><?= htmlspecialchars($currentUser['account_type']) ?> Account</p>
    </div>

    <div class="stat-card bg-white rounded-xl p-6 shadow-sm border border-slate-100">
        <p class="text-slate-500 text-sm font-medium mb-1">Total Deposited</p>
        <p class="text-3xl font-bold text-green-600">$<?= number_format((float)($stats['total_in'] ?? 0), 2) ?></p>
        <p class="text-slate-400 text-xs mt-2">All time</p>
    </div>

    <div class="stat-card bg-white rounded-xl p-6 shadow-sm border border-slate-100">
        <p class="text-slate-500 text-sm font-medium mb-1">Total Withdrawn</p>
        <p class="text-3xl font-bold text-red-600">$<?= number_format((float)($stats['total_out'] ?? 0), 2) ?></p>
        <p class="text-slate-400 text-xs mt-2"><?= (int)($stats['total_tx'] ?? 0) ?> total transaction<?= $stats['total_tx'] != 1 ? 's' : '' ?></p>
    </div>
</div>

<!-- ── Quick Actions ─────────────────────────── -->
<div class="bg-white rounded-xl p-6 shadow-sm border border-slate-100 mb-8">
    <h3 class="text-slate-700 font-semibold mb-4">Quick Actions</h3>
    <div class="flex flex-wrap gap-3">
        <a href="<?= BASE_URL ?>/pages/deposit.php"
           class="px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-semibold transition-colors">
            💰 Deposit
        </a>
        <a href="<?= BASE_URL ?>/pages/withdraw.php"
           class="px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-semibold transition-colors">
            💸 Withdraw
        </a>
        <a href="<?= BASE_URL ?>/pages/transfer.php"
           class="px-5 py-2.5 bg-orange-600 hover:bg-orange-700 text-white rounded-lg text-sm font-semibold transition-colors">
            🔄 Transfer
        </a>
        <a href="<?= BASE_URL ?>/pages/history.php"
           class="px-5 py-2.5 bg-slate-600 hover:bg-slate-700 text-white rounded-lg text-sm font-semibold transition-colors">
            📋 Full History
        </a>
    </div>
</div>

<!-- ── Recent Transactions ───────────────────── -->
<div class="bg-white rounded-xl shadow-sm border border-slate-100">
    <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
        <h3 class="text-slate-700 font-semibold">Recent Transactions</h3>
        <a href="<?= BASE_URL ?>/pages/history.php" class="text-blue-600 text-sm hover:underline">View all &rarr;</a>
    </div>

    <?php if (empty($recentTx)): ?>
    <div class="px-6 py-14 text-center text-slate-400">
        <p class="text-4xl mb-3">📭</p>
        <p class="font-medium">No transactions yet.</p>
        <p class="text-sm mt-1">Make your first deposit to get started!</p>
    </div>
    <?php else: ?>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="text-xs text-slate-500 uppercase bg-slate-50 border-b border-slate-100">
                    <th class="px-6 py-3 text-left">Type</th>
                    <th class="px-6 py-3 text-left">Description</th>
                    <th class="px-6 py-3 text-right">Amount</th>
                    <th class="px-6 py-3 text-right">Balance After</th>
                    <th class="px-6 py-3 text-right">Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recentTx as $tx):
                    $m = getTxMeta($tx['type']);
                ?>
                <tr class="tx-row border-b border-slate-50 last:border-0">
                    <td class="px-6 py-3">
                        <span class="<?= $m['class'] ?> font-semibold text-sm"><?= $m['label'] ?></span>
                    </td>
                    <td class="px-6 py-3 text-slate-500 text-sm max-w-xs truncate">
                        <?= htmlspecialchars($tx['description'] ?? '—') ?>
                    </td>
                    <td class="px-6 py-3 text-right font-bold text-sm <?= $m['class'] ?>">
                        <?= $m['sign'] ?>$<?= number_format((float)$tx['amount'], 2) ?>
                    </td>
                    <td class="px-6 py-3 text-right text-slate-600 text-sm font-mono">
                        $<?= number_format((float)$tx['balance_after'], 2) ?>
                    </td>
                    <td class="px-6 py-3 text-right text-slate-400 text-xs">
                        <?= date('M j, Y g:i A', strtotime($tx['created_at'])) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
