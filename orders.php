<?php
// This shows all orders related to the logged in user

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

requireLogin();

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['action'])) {
    $order_id = $_POST['order_id'];
    $action = $_POST['action'];

    // Only allow valid status changes
    $allowed = ['accepted', 'rejected', 'completed', 'cancelled'];
    if (in_array($action, $allowed)) {
        $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ? AND seller_id = ?");
        $stmt->execute([$action, $order_id, $user_id]);
    }
}

$stmt = $pdo->prepare("SELECT orders.*, listings.title, listings.price, users.name AS seller_name 
                        FROM orders 
                        JOIN listings ON orders.listing_id = listings.id 
                        JOIN users ON orders.seller_id = users.id 
                        WHERE orders.buyer_id = ? 
                        ORDER BY orders.created_at DESC");
$stmt->execute([$user_id]);
$buying = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("SELECT orders.*, listings.title, listings.price, users.name AS buyer_name 
                        FROM orders 
                        JOIN listings ON orders.listing_id = listings.id 
                        JOIN users ON orders.buyer_id = users.id 
                        WHERE orders.seller_id = ? 
                        ORDER BY orders.created_at DESC");
$stmt->execute([$user_id]);
$selling = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders - ReTrade</title>
    <link rel="stylesheet" href="/c2c-retrade/assets/css/style.css">
    <style>
        .orders-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 40px;
            background: #fff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        .orders-table th {
            background: #1a1a2e;
            color: #fff;
            padding: 14px;
            text-align: left;
            font-size: 14px;
        }
        .orders-table td {
            padding: 14px;
            border-bottom: 1px solid #f0f0f0;
            font-size: 14px;
        }
        .status-pending    { color: #f4a261; font-weight:600; }
        .status-accepted   { color: #2dc653; font-weight:600; }
        .status-rejected   { color: #e63946; font-weight:600; }
        .status-completed  { color: #00b4d8; font-weight:600; }
        .status-cancelled  { color: #888;    font-weight:600; }
    </style>
</head>
<body>

<?php require_once __DIR__ . '/includes/header.php'; ?>

<div class="container">
    <h2 class="page-title">My Purchases</h2>
    <?php if (count($buying) > 0): ?>
        <table class="orders-table">
            <tr>
                <th>Item</th>
                <th>Price</th>
                <th>Seller</th>
                <th>Status</th>
                <th>Date</th>
            </tr>
            <?php foreach ($buying as $order): ?>
                <tr>
                    <td><?= htmlspecialchars($order['title']) ?></td>
                    <td>R<?= number_format($order['price'], 2) ?></td>
                    <td><?= htmlspecialchars($order['seller_name']) ?></td>
                    <td class="status-<?= $order['status'] ?>"><?= ucfirst($order['status']) ?></td>
                    <td><?= date('d M Y', strtotime($order['created_at'])) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p style="margin-bottom:40px;">You have not bought anything yet.</p>
    <?php endif; ?>

    <h2 class="page-title">My Sales</h2>
    <?php if (count($selling) > 0): ?>
        <table class="orders-table">
            <tr>
                <th>Item</th>
                <th>Price</th>
                <th>Buyer</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
            <?php foreach ($selling as $order): ?>
                <tr>
                    <td><?= htmlspecialchars($order['title']) ?></td>
                    <td>R<?= number_format($order['price'], 2) ?></td>
                    <td><?= htmlspecialchars($order['buyer_name']) ?></td>
                    <td class="status-<?= $order['status'] ?>"><?= ucfirst($order['status']) ?></td>
                    <td>
                        <!-- Seller can accept or reject pending orders -->
                        <?php if ($order['status'] === 'pending'): ?>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                <button name="action" value="accepted" class="btn btn-success" 
                                        style="padding:6px 12px; font-size:13px;">Accept</button>
                                <button name="action" value="rejected" class="btn btn-danger" 
                                        style="padding:6px 12px; font-size:13px;">Reject</button>
                            </form>
                        <?php elseif ($order['status'] === 'accepted'): ?>
                            <!-- Once accepted seller can mark it as completed -->
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                <button name="action" value="completed" class="btn btn-primary" 
                                        style="padding:6px 12px; font-size:13px;">Mark Complete</button>
                            </form>
                        <?php else: ?>
                            <span style="color:#888;">No actions</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>You have no incoming orders yet.</p>
    <?php endif; ?>
</div>

<footer>
    <p>&copy; 2026 ReTrade. All rights reserved.</p>
</footer>

<script src="/c2c-retrade/assets/js/main.js"></script>
</body>
</html>