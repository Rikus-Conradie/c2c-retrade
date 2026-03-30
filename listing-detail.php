<?php
// This page shows the full details of a listing

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_GET['id'])) {
    header("Location: listings.php");
    exit();
}

$id = $_GET['id'];

// The listing and the sellers name
$stmt = $pdo->prepare("SELECT listings.*, users.name AS seller_name, users.id AS seller_id 
                        FROM listings 
                        JOIN users ON listings.user_id = users.id 
                        WHERE listings.id = ?");
$stmt->execute([$id]);
$listing = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$listing) {
    header("Location: listings.php");
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isLoggedIn()) {
    // Make sure the buyer is not the seller
    if ($_SESSION['user_id'] == $listing['seller_id']) {
        $error = "You cannot buy your own listing.";
    } else {
        $stmt = $pdo->prepare("SELECT id FROM orders WHERE listing_id = ? AND buyer_id = ? AND status = 'pending'");
        $stmt->execute([$id, $_SESSION['user_id']]);

        if ($stmt->fetch()) {
            $error = "You already have a pending order for this item.";
        } else {
            // Create the order
            $stmt = $pdo->prepare("INSERT INTO orders (listing_id, buyer_id, seller_id) VALUES (?, ?, ?)");
            $stmt->execute([$id, $_SESSION['user_id'], $listing['seller_id']]);
            $success = "Order placed successfully! The seller will respond shortly.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($listing['title']) ?> - ReTrade</title>
    <link rel="stylesheet" href="/c2c-retrade/assets/css/style.css">
</head>
<body>

<?php require_once __DIR__ . '/includes/header.php'; ?>

<div class="container">
    <?php if ($error): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <div class="listing-detail">
        <!-- Listing image -->
        <?php if ($listing['image']): ?>
            <img src="/c2c-retrade/assets/images/<?= htmlspecialchars($listing['image']) ?>" 
                 alt="<?= htmlspecialchars($listing['title']) ?>"
                 style="width:100%; max-height:400px; object-fit:cover; border-radius:10px; margin-bottom:30px;">
        <?php else: ?>
            <div style="width:100%; height:250px; background:#eee; border-radius:10px; 
                        display:flex; align-items:center; justify-content:center; 
                        margin-bottom:30px; color:#888; font-size:18px;">
                No image provided
            </div>
        <?php endif; ?>

        <h1 style="font-size:28px; color:#1a1a2e; margin-bottom:10px;">
            <?= htmlspecialchars($listing['title']) ?>
        </h1>

        <p style="font-size:26px; color:#00b4d8; font-weight:700; margin-bottom:15px;">
            R<?= number_format($listing['price'], 2) ?>
        </p>

        <p style="color:#888; margin-bottom:20px;">
            Category: <?= htmlspecialchars($listing['category']) ?> &nbsp;|&nbsp;
            Sold by: <?= htmlspecialchars($listing['seller_name']) ?>
        </p>

        <p style="font-size:16px; line-height:1.8; margin-bottom:30px;">
            <?= nl2br(htmlspecialchars($listing['description'])) ?>
        </p>

        <!-- Show buy button only if logged in and not the seller -->
        <?php if (isLoggedIn() && $_SESSION['user_id'] != $listing['seller_id']): ?>
            <form method="POST" action="">
                <button type="submit" class="btn btn-primary" style="font-size:17px; padding:14px 40px;">
                    Buy Now
                </button>
            </form>
        <?php elseif (!isLoggedIn()): ?>
            <a href="login.php" class="btn btn-primary">Login to Buy</a>
        <?php else: ?>
            <p style="color:#888;">This is your own listing.</p>
        <?php endif; ?>
    </div>
</div>

<footer>
    <p>&copy; 2026 ReTrade. All rights reserved.</p>
</footer>

<script src="/c2c-retrade/assets/js/main.js"></script>
</body>
</html>