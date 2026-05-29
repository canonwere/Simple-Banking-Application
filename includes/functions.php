<?php
function generateAccountNumber(): string {
    return 'SBA-' . str_pad(mt_rand(0, 9999999), 7, '0', STR_PAD_LEFT);
}

function setFlash(string $type, string $message): void {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash(): ?array {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

function logTransaction(
    PDO $pdo,
    int $accountId,
    string $type,
    float $amount,
    float $balanceAfter,
    string $description = '',
    ?string $relatedAccount = null
): void {
    $stmt = $pdo->prepare("
        INSERT INTO transactions (account_id, type, amount, balance_after, description, related_account)
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([$accountId, $type, $amount, $balanceAfter, $description ?: null, $relatedAccount]);
}

function getTxMeta(string $type): array {
    return match ($type) {
        'deposit'      => ['label' => 'Deposit',      'class' => 'tx-deposit',  'sign' => '+'],
        'withdrawal'   => ['label' => 'Withdrawal',   'class' => 'tx-withdraw', 'sign' => '-'],
        'transfer_in'  => ['label' => 'Transfer In',  'class' => 'tx-deposit',  'sign' => '+'],
        'transfer_out' => ['label' => 'Transfer Out', 'class' => 'tx-transfer', 'sign' => '-'],
        default        => ['label' => ucfirst($type), 'class' => '',            'sign' => ''],
    };
}
