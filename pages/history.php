<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
requireLogin();

$pageTitle  = 'Transaction History';
$activePage = 'history';

$allowed = ['all', 'deposit', 'withdrawal', 'transfer_in', 'transfer_out'];
$filter  = in_array($_GET['type'] ?? '', $allowed) ? $_GET['type'] : 'all';

require_once __DIR__ . '/../includes/header.php';

if ($filter === 'all') {
    $stmt = $pdo->prepare("SELECT * FROM transactions WHERE account_id = ? ORDER BY created_at DESC");
    $stmt->execute([$currentUser['account_id']]);
} else {
    $stmt = $pdo->prepare("SELECT * FROM transactions WHERE account_id = ? AND type = ? ORDER BY created_at DESC");
    $stmt->execute([$currentUser['account_id'], $filter]);
}
$transactions = $stmt->fetchAll();
?>

<!-- ── Filter Tabs ───────────────────────────── -->
<div class="flex flex-wrap gap-2 mb-6">
    <?php
    $tabs = [
        'all'          => 'All',
        'deposit'      => '💰 Deposits',
        'withdrawal'   => '💸 Withdrawals',
        'transfer_in'  => '🔼 Transfers In',
        'transfer_out' => '🔽 Transfers Out',
    ];
    foreach ($tabs as $val => $label):
    ?>
    <a href="?type=<?= $val ?>"
       class="px-4 py-2 rounded-full text-sm font-medium transition-colors
              <?= $filter === $val
                  ? 'bg-blue-600 text-white shadow-sm'
                  : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' ?>">
        <?= $label ?>
    </a>
    <?php endforeach; ?>
</div>

<!-- ── Table ─────────────────────────────────── -->
<div class="bg-white rounded-xl shadow-sm border border-slate-100">
    <div class="px-6 py-4 border-b border-slate-100">
        <span class="text-slate-700 font-semibold">
            <?= number_format(count($transactions)) ?> transaction<?= count($transactions) !== 1 ? 's' : '' ?>
        </span>
    </div>

    <?php if (empty($transactions)): ?>
    <div class="px-6 py-14 text-center text-slate-400">
        <p class="text-4xl mb-3">📭</p>
        <p class="font-medium">No transactions found.</p>
    </div>
    <?php else: ?>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="text-xs text-slate-500 uppercase bg-slate-50 border-b border-slate-100">
                    <th class="px-6 py-3 text-left w-12">#</th>
                    <th class="px-6 py-3 text-left">Type</th>
                    <th class="px-6 py-3 text-left">Description</th>
                    <th class="px-6 py-3 text-left">Related Account</th>
                    <th class="px-6 py-3 text-right">Amount</th>
                    <th class="px-6 py-3 text-right">Balance After</th>
                    <th class="px-6 py-3 text-right">Date &amp; Time</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($transactions as $tx):
                    $m = getTxMeta($tx['type']);
                ?>
                <tr class="tx-row border-b border-slate-50 last:border-0">
                    <td class="px-6 py-3 text-slate-400 text-xs"><?= $tx['id'] ?></td>
                    <td class="px-6 py-3">
                        <span class="<?= $m['class'] ?> font-semibold text-sm"><?= $m['label'] ?></span>
                    </td>
                    <td class="px-6 py-3 text-slate-600 text-sm max-w-xs truncate">
                        <?= htmlspecialchars($tx['description'] ?? '—') ?>
                    </td>
                    <td class="px-6 py-3 text-slate-500 text-sm font-mono">
                        <?= htmlspecialchars($tx['related_account'] ?? '—') ?>
                    </td>
                    <td class="px-6 py-3 text-right font-bold text-sm <?= $m['class'] ?>">
                        <?= $m['sign'] ?>$<?= number_format((float)$tx['amount'], 2) ?>
                    </td>
                    <td class="px-6 py-3 text-right text-slate-600 text-sm font-mono">
                        $<?= number_format((float)$tx['balance_after'], 2) ?>
                    </td>
                    <td class="px-6 py-3 text-right">
                        <span class="text-slate-600 text-xs"><?= date('M j, Y', strtotime($tx['created_at'])) ?></span><br>
                        <span class="text-slate-400 text-xs"><?= date('g:i A', strtotime($tx['created_at'])) ?></span>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
