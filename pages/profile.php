<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'update_profile') {
        $name  = trim($_POST['name']  ?? '');
        $email = trim($_POST['email'] ?? '');

        if (!$name || !$email) {
            setFlash('error', 'Name and email are required.');
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            setFlash('error', 'Please enter a valid email address.');
        } else {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
            $stmt->execute([$email, $_SESSION['user_id']]);
            if ($stmt->fetch()) {
                setFlash('error', 'That email is already used by another account.');
            } else {
                $pdo->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?")
                    ->execute([$name, $email, $_SESSION['user_id']]);
                setFlash('success', 'Profile updated successfully.');
            }
        }

    } elseif ($action === 'change_password') {
        $current = $_POST['current_password'] ?? '';
        $new     = $_POST['new_password']     ?? '';
        $confirm = $_POST['confirm_password'] ?? '';

        $stmt = $pdo->prepare("SELECT password_hash FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $row = $stmt->fetch();

        if (!password_verify($current, $row['password_hash'])) {
            setFlash('error', 'Current password is incorrect.');
        } elseif (strlen($new) < 6) {
            setFlash('error', 'New password must be at least 6 characters.');
        } elseif ($new !== $confirm) {
            setFlash('error', 'New passwords do not match.');
        } else {
            $pdo->prepare("UPDATE users SET password_hash = ? WHERE id = ?")
                ->execute([password_hash($new, PASSWORD_DEFAULT), $_SESSION['user_id']]);
            setFlash('success', 'Password changed successfully.');
        }
    }

    header('Location: ' . BASE_URL . '/pages/profile.php');
    exit;
}

$pageTitle  = 'Profile';
$activePage = 'profile';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="max-w-2xl space-y-6">

    <!-- Account Info -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6">
        <h3 class="text-slate-700 font-semibold mb-4">Account Information</h3>
        <div class="grid grid-cols-2 gap-5">
            <div>
                <p class="text-xs text-slate-500 uppercase tracking-wide mb-1">Account Number</p>
                <p class="font-mono font-semibold text-slate-800"><?= htmlspecialchars($currentUser['account_number']) ?></p>
            </div>
            <div>
                <p class="text-xs text-slate-500 uppercase tracking-wide mb-1">Account Type</p>
                <p class="font-semibold text-slate-800 capitalize"><?= htmlspecialchars($currentUser['account_type']) ?></p>
            </div>
            <div>
                <p class="text-xs text-slate-500 uppercase tracking-wide mb-1">Status</p>
                <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-semibold
                    <?= $currentUser['status'] === 'active'
                        ? 'bg-green-100 text-green-700'
                        : 'bg-red-100 text-red-700' ?>">
                    <?= ucfirst(htmlspecialchars($currentUser['status'])) ?>
                </span>
            </div>
            <div>
                <p class="text-xs text-slate-500 uppercase tracking-wide mb-1">Member Since</p>
                <p class="font-semibold text-slate-800"><?= date('F j, Y', strtotime($currentUser['member_since'])) ?></p>
            </div>
        </div>
    </div>

    <!-- Edit Profile -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6">
        <h3 class="text-slate-700 font-semibold mb-4">Edit Profile</h3>
        <form method="POST" novalidate>
            <input type="hidden" name="action" value="update_profile">
            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-1">Full Name</label>
                <input type="text" name="name" class="form-input"
                       value="<?= htmlspecialchars($currentUser['name']) ?>" required>
            </div>
            <div class="mb-6">
                <label class="block text-sm font-medium text-slate-700 mb-1">Email Address</label>
                <input type="email" name="email" class="form-input"
                       value="<?= htmlspecialchars($currentUser['email']) ?>" required>
            </div>
            <button type="submit" class="btn-primary" style="max-width:180px">Save Changes</button>
        </form>
    </div>

    <!-- Change Password -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6">
        <h3 class="text-slate-700 font-semibold mb-4">Change Password</h3>
        <form method="POST" novalidate>
            <input type="hidden" name="action" value="change_password">
            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-1">Current Password</label>
                <input type="password" name="current_password" class="form-input" required>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-1">New Password</label>
                <input type="password" name="new_password" class="form-input"
                       placeholder="At least 6 characters" required>
            </div>
            <div class="mb-6">
                <label class="block text-sm font-medium text-slate-700 mb-1">Confirm New Password</label>
                <input type="password" name="confirm_password" class="form-input" required>
            </div>
            <button type="submit" class="btn-primary btn-danger" style="max-width:200px">Change Password</button>
        </form>
    </div>

</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
