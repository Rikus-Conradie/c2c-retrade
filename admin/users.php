<?php
// Admin page to view and manage all users
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

requireLogin();
if (!isAdmin() && !isModerator() && !isSupport()) {
    header("Location: ../index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isAdmin()) {
    $user_id = $_POST['user_id'];
    $role = $_POST['role'];
    $allowed_roles = ['user', 'admin', 'moderator', 'support'];

    if (in_array($role, $allowed_roles)) {
        $stmt = $pdo->prepare("UPDATE users SET role = ? WHERE id = ?");
        $stmt->execute([$role, $user_id]);
    }
}

$stmt = $pdo->query("SELECT * FROM users ORDER BY created_at DESC");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - ReTrade Admin</title>
    <link rel="stylesheet" href="/c2c-retrade/assets/css/style.css">
    <style>
        .admin-container { max-width:1200px; margin:40px auto; padding:0 20px; }
        .admin-nav { display:flex; gap:15px; margin-bottom:30px; flex-wrap:wrap; }
        .admin-nav a { background:#1a1a2e; color:#fff; padding:10px 20px; border-radius:6px; font-size:14px; }
        .admin-nav a:hover { background:#00b4d8; }
        .admin-table { width:100%; border-collapse:collapse; background:#fff; border-radius:10px; overflow:hidden; box-shadow:0 2px 8px rgba(0,0,0,0.08); }
        .admin-table th { background:#1a1a2e; color:#fff; padding:14px; text-align:left; font-size:14px; }
        .admin-table td { padding:14px; border-bottom:1px solid #f0f0f0; font-size:14px; }
    </style>
</head>
<body>

<?php require_once __DIR__ . '/../includes/header.php'; ?>

<div class="admin-container">
    <h2 class="page-title">Manage Users</h2>

    <div class="admin-nav">
        <a href="index.php">Dashboard</a>
        <a href="users.php">Manage Users</a>
        <a href="listings.php">Manage Listings</a>
        <a href="orders.php">Manage Orders</a>
    </div>

    <table class="admin-table">
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Joined</th>
            <?php if (isAdmin()): ?>
                <th>Change Role</th>
            <?php endif; ?>
        </tr>
        <?php foreach ($users as $user): ?>
            <tr>
                <td><?= $user['id'] ?></td>
                <td><?= htmlspecialchars($user['name']) ?></td>
                <td><?= htmlspecialchars($user['email']) ?></td>
                <td><strong style="color:#00b4d8;"><?= ucfirst($user['role']) ?></strong></td>
                <td><?= date('d M Y', strtotime($user['created_at'])) ?></td>
                <?php if (isAdmin()): ?>
                    <td>
                        <!-- Admin can change any user's role -->
                        <form method="POST" style="display:flex; gap:8px;">
                            <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                            <select name="role" style="padding:6px; border-radius:4px; border:1px solid #ddd;">
                                <?php foreach (['user','admin','moderator','support'] as $r): ?>
                                    <option value="<?= $r ?>" <?= $user['role'] === $r ? 'selected' : '' ?>>
                                        <?= ucfirst($r) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit" class="btn btn-primary" style="padding:6px 12px; font-size:13px;">Save</button>
                        </form>
                    </td>
                <?php endif; ?>
            </tr>
        <?php endforeach; ?>
    </table>
</div>

<footer>
    <p>&copy; 2026 ReTrade. All rights reserved.</p>
</footer>

<script src="/c2c-retrade/assets/js/main.js"></script>
</body>
</html>