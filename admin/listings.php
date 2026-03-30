<?php
// Admin page to view and manage all listings on the platform
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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['listing_id'], $_POST['action'])) {
    $listing_id = $_POST['listing_id'];
    $action = $_POST['action'];
    $allowed = ['active', 'sold', 'removed'];

    if (in_array($action, $allowed)) {
        $stmt = $pdo->prepare("UPDATE listings SET status = ? WHERE id = ?");
        $stmt->execute([$action, $listing_id]);
    }
}

$stmt = $pdo->query("SELECT listings.*, users.name AS seller_name 
                     FROM listings 
                     JOIN users ON listings.user_id = users.id 
                     ORDER BY listings.created_at DESC");
$listings = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Listings - ReTrade Admin</title>
    <link rel="stylesheet" href="/c2c-retrade/assets/css/style.css">
    <style>
        .admin-container { max-width:1200px; margin:40px auto; padding:0 20px; }
        .admin-nav { display:flex; gap:15px; margin-bottom:30px; flex-wrap:wrap; }
        .admin-nav a { background:#1a1a2e; color:#fff; padding:10px 20px; border-radius:6px; font-size:14px; }
        .admin-nav a:hover { background:#00b4d8; }
        .admin-table { width:100%; border-collapse:collapse; background:#fff; border-radius:10px; overflow:hidden; box-shadow:0 2px 8px rgba(0,0,0,0.08); }
        .admin-table th { background:#1a1a2e; color:#fff; padding:14px; text-align:left; font-size:14px; }
        .admin-table td { padding:14px; border-bottom:1px solid #f0f0f0; font-size:14px; }
        .status-active  { color:#2dc653; font-weight:600; }
        .status-sold    { color:#00b4d8; font-weight:600; }
        .status-removed { color:#e63946; font-weight:600; }
    </style>
</head>
<body>

<?php require_once __DIR__ . '/../includes/header.php'; ?>

<div class="admin-container">
    <h2 class="page-title">Manage Listings</h2>

    <div class="admin-nav">
        <a href="index.php">Dashboard</a>
        <a href="users.php">Manage Users</a>
        <a href="listings.php">Manage Listings</a>
        <a href="orders.php">Manage Orders</a>
    </div>

    <table class="admin-table">
        <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Seller</th>
            <th>Price</th>
            <th>Category</th>
            <th>Status</th>
            <th>Date</th>
            <th>Action</th>
        </tr>
        <?php foreach ($listings as $listing): ?>
            <tr>
                <td><?= $listing['id'] ?></td>
                <td><?= htmlspecialchars($listing['title']) ?></td>
                <td><?= htmlspecialchars($listing['seller_name']) ?></td>
                <td>R<?= number_format($listing['price'], 2) ?></td>
                <td><?= htmlspecialchars($listing['category']) ?></td>
                <td class="status-<?= $listing['status'] ?>"><?= ucfirst($listing['status']) ?></td>
                <td><?= date('d M Y', strtotime($listing['created_at'])) ?></td>
                <td>
                    <!-- Moderators and admins can change listing status -->
                    <form method="POST" style="display:flex; gap:6px;">
                        <input type="hidden" name="listing_id" value="<?= $listing['id'] ?>">
                        <?php if ($listing['status'] !== 'active'): ?>
                            <button name="action" value="active" class="btn btn-success" 
                                    style="padding:6px 10px; font-size:12px;">Activate</button>
                        <?php endif; ?>
                        <?php if ($listing['status'] !== 'removed'): ?>
                            <button name="action" value="removed" class="btn btn-danger" 
                                    style="padding:6px 10px; font-size:12px;">Remove</button>
                        <?php endif; ?>
                    </form>
                </td>
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