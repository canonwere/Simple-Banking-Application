<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = (float)($_POST['amount'] ?? 0);
    $desc   = trim($_POST['description'] ?? '');

    if ($amount < 0.01 || $amount > 1_000_000) {
        setFlash('error', 'Please enter a valid amount between $0.01 and $1,000,000.');
    } else {
        $stmt = $pdo->prepare("SELECT id, balance FROM accounts WHERE user_id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $account = $stmt->fetch();

        $newBalance = $account['balance'] + $amount;
        $pdo->prepare("UPDATE accounts SET balance = ? WHERE id = ?")->execute([$newBalance, $account['id']]);
        logTransaction($pdo, $account['id'], 'deposit', $amount, $newBalance, $desc ?: 'Deposit');

        setFlash('success', 'Successfully deposited $' . number_format($amount, 2) . '.');
        header('Location: ' . BASE_URL . '/pages/deposit.php');
        exit;
    }
}

$pageTitle  = 'Deposit';
$activePage = 'deposit';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="max-w-lg">
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-8">

        <div class="flex items-center gap-4 mb-6">
            <span class="text-4xl">💰</span>
            <div>
                <h3 class="text-lg font-semibold text-slate-800">Make a Deposit</h3>
                <p class="text-slate-500 text-sm">Add funds to your account</p>
            </div>
        </div>

        <!-- Balance display -->
        <div class="bg-blue-50 border border-blue-100 rounded-lg px-5 py-4 mb-6">
            <p class="text-blue-500 text-sm">Current Balance</p>
            <p class="text-3xl font-bold text-blue-800">$<?= number_format((float)$currentUser['balance'], 2) ?></p>
        </div>

        <form method="POST" novalidate>
            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-1">Amount</label>
                <div class="input-prefix">
                    <span>$</span>
                    <input type="number" name="amount" class="form-input"
                           placeholder="0.00" min="0.01" max="1000000" step="0.01"
                           required autofocus>
                </div>
            </div>
            <div class="mb-6">
                <label class="block text-sm font-medium text-slate-700 mb-1">
                    Description <span class="text-slate-400 font-normal">(optional)</span>
                </label>
                <input type="text" name="description" class="form-input"
                       placeholder="e.g. Salary, ATM deposit, Gift…">
            </div>
            <button type="submit" class="btn-primary btn-success">Deposit Funds</button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
