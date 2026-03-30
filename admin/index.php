<?php
// This is the main admin dashboard
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// must be Admin. Kicks anybody else 
requireLogin();
if (!isAdmin() && !isModerator() && !isSupport()) {
    header("Location: ../index.php");
    exit();
}

// Count total users
$stmt = $pdo->query("SELECT COUNT(*) FROM users");
$total_users = $stmt->fetchColumn();

// Count total listings
$stmt = $pdo->query("SELECT COUNT(*) FROM listings");
$total_listings = $stmt->fetchColumn();

// Count total orders
$stmt = $pdo->query("SELECT COUNT(*) FROM orders");
$total_orders = $stmt->fetchColumn();

// Count pending orders
$stmt = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'pending'");
$pending_orders = $stmt->fetchColumn();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - ReTrade</title>
    <link rel="stylesheet" href="/c2c-retrade/assets/css/style.css">
    <style>
        .admin-container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }
        .stat-card {
            background: #fff;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            text-align: center;
        }
        .stat-card h3 {
            font-size: 38px;
            color: #00b4d8;
            margin-bottom: 8px;
        }
        .stat-card p {
            color: #888;
            font-size: 14px;
        }
        .admin-nav {
            display: flex;
            gap: 15px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }
        .admin-nav a {
            background: #1a1a2e;
            color: #fff;
            padding: 10px 20px;
            border-radius: 6px;
            font-size: 14px;
            transition: background 0.3s;
        }
        .admin-nav a:hover {
            background: #00b4d8;
        }
    </style>
</head>
<body>

<?php require_once __DIR__ . '/../includes/header.php'; ?>

<div class="admin-container">
    <h2 class="page-title">Admin Dashboard</h2>

    <!-- Admin Navigation -->
    <div class="admin-nav">
        <a href="index.php">Dashboard</a>
        <a href="users.php">Manage Users</a>
        <a href="listings.php">Manage Listings</a>
        <a href="orders.php">Manage Orders</a>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <h3><?= $total_users ?></h3>
            <p>Total Users</p>
        </div>
        <div class="stat-card">
            <h3><?= $total_listings ?></h3>
            <p>Total Listings</p>
        </div>
        <div class="stat-card">
            <h3><?= $total_orders ?></h3>
            <p>Total Orders</p>
        </div>
        <div class="stat-card">
            <h3><?= $pending_orders ?></h3>
            <p>Pending Orders</p>
        </div>
    </div>

    <p style="color:#888; font-size:14px;">
        Logged in as: <strong><?= htmlspecialchars($_SESSION['user_name']) ?></strong> 
        &nbsp;|&nbsp; Role: <strong style="color:#00b4d8;"><?= ucfirst($_SESSION['role']) ?></strong>
    </p>
</div>

<footer>
    <p>&copy; 2026 ReTrade. All rights reserved.</p>
</footer>

<script src="/c2c-retrade/assets/js/main.js"></script>
</body>
</html>