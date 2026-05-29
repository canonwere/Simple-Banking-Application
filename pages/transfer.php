<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $toAccNum = strtoupper(trim($_POST['to_account'] ?? ''));
    $amount   = (float)($_POST['amount'] ?? 0);
    $desc     = trim($_POST['description'] ?? '');

    $stmt = $pdo->prepare("SELECT id, balance, account_number FROM accounts WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $from = $stmt->fetch();

    if (empty($toAccNum)) {
        setFlash('error', 'Please enter a recipient account number.');
    } elseif ($toAccNum === $from['account_number']) {
        setFlash('error', 'You cannot transfer to your own account.');
    } elseif ($amount < 0.01 || $amount > 1_000_000) {
        setFlash('error', 'Please enter a valid amount between $0.01 and $1,000,000.');
    } elseif ($amount > (float)$from['balance']) {
        setFlash('error', 'Insufficient funds. Available: $' . number_format((float)$from['balance'], 2) . '.');
    } else {
        $stmt = $pdo->prepare("
            SELECT a.id, a.account_number, a.balance, u.name
            FROM accounts a JOIN users u ON a.user_id = u.id
            WHERE a.account_number = ?
        ");
        $stmt->execute([$toAccNum]);
        $to = $stmt->fetch();

        if (!$to) {
            setFlash('error', 'Account "' . htmlspecialchars($toAccNum) . '" not found. Please check the number.');
        } else {
            $pdo->beginTransaction();
            try {
                // Debit sender
                $fromNew = $from['balance'] - $amount;
                $pdo->prepare("UPDATE accounts SET balance = ? WHERE id = ?")->execute([$fromNew, $from['id']]);
                logTransaction($pdo, $from['id'], 'transfer_out', $amount, $fromNew,
                    $desc ?: 'Transfer to ' . $to['account_number'], $to['account_number']);

                // Credit recipient
                $toNew = $to['balance'] + $amount;
                $pdo->prepare("UPDATE accounts SET balance = ? WHERE id = ?")->execute([$toNew, $to['id']]);
                logTransaction($pdo, $to['id'], 'transfer_in', $amount, $toNew,
                    'Transfer from ' . $from['account_number'], $from['account_number']);

                $pdo->commit();
                setFlash('success',
                    'Transferred $' . number_format($amount, 2) .
                    ' to ' . $to['name'] . ' (' . $to['account_number'] . ').'
                );
                header('Location: ' . BASE_URL . '/pages/transfer.php');
                exit;
            } catch (Exception $e) {
                $pdo->rollBack();
                setFlash('error', 'Transfer failed unexpectedly. Please try again.');
            }
        }
    }
}

$pageTitle  = 'Transfer';
$activePage = 'transfer';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="max-w-lg space-y-4">
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-8">

        <div class="flex items-center gap-4 mb-6">
            <span class="text-4xl">🔄</span>
            <div>
                <h3 class="text-lg font-semibold text-slate-800">Transfer Funds</h3>
                <p class="text-slate-500 text-sm">Send money to another account</p>
            </div>
        </div>

        <div class="bg-blue-50 border border-blue-100 rounded-lg px-5 py-4 mb-6">
            <p class="text-blue-500 text-sm">Available Balance</p>
            <p class="text-3xl font-bold text-blue-800">$<?= number_format((float)$currentUser['balance'], 2) ?></p>
            <p class="text-blue-400 text-xs mt-1 font-mono">Your account: <?= htmlspecialchars($currentUser['account_number']) ?></p>
        </div>

        <form method="POST" novalidate>
            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-1">Recipient Account Number</label>
                <input type="text" name="to_account" class="form-input font-mono"
                       placeholder="SBA-0000000"
                       value="<?= htmlspecialchars($_POST['to_account'] ?? '') ?>"
                       required autofocus>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-1">Amount</label>
                <div class="input-prefix">
                    <span>$</span>
                    <input type="number" name="amount" class="form-input"
                           placeholder="0.00" min="0.01"
                           max="<?= htmlspecialchars($currentUser['balance']) ?>"
                           step="0.01" required>
                </div>
            </div>
            <div class="mb-6">
                <label class="block text-sm font-medium text-slate-700 mb-1">
                    Description <span class="text-slate-400 font-normal">(optional)</span>
                </label>
                <input type="text" name="description" class="form-input"
                       placeholder="e.g. Rent split, loan repayment…">
            </div>
            <button type="submit" class="btn-primary btn-warning">Transfer Funds</button>
        </form>
    </div>

    <div class="bg-amber-50 border border-amber-200 rounded-lg px-5 py-3">
        <p class="text-amber-800 text-sm">
            <strong>Note:</strong> Transfers are instant and cannot be reversed.
            Double-check the account number before proceeding.
        </p>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
