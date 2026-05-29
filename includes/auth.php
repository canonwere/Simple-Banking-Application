<?php
function requireLogin(): void {
    if (!isset($_SESSION['user_id'])) {
        header('Location: ' . BASE_URL . '/index.php');
        exit;
    }
}

function getCurrentUser(PDO $pdo): ?array {
    if (!isset($_SESSION['user_id'])) return null;
    $stmt = $pdo->prepare("
        SELECT u.id, u.name, u.email, u.created_at AS member_since,
               a.id AS account_id, a.account_number, a.balance,
               a.account_type, a.status
        FROM users u
        JOIN accounts a ON u.id = a.user_id
        WHERE u.id = ?
    ");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch() ?: null;
}
