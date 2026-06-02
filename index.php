<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';

$stmt = $pdo->query("SELECT listings.*, users.name AS seller_name
                     FROM listings
                     JOIN users ON listings.user_id = users.id
                     WHERE listings.status = 'active'
                     ORDER BY listings.created_at DESC
                     LIMIT 6");
$listings = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ReTrade - C2C Marketplace</title>
    <link rel="stylesheet" href="/c2c-retrade/assets/css/style.css">
</head>
<body>

<?php require_once __DIR__ . '/includes/header.php'; ?>

<section class="hero">
    <div class="hero-content">
        <h1>Buy and Sell Second-Hand Goods</h1>
        <p>A safe and structured marketplace for your community</p>
        <a href="listings.php" class="btn btn-primary">Browse Listings</a>
        <?php if (!isLoggedIn()): ?>
            <a href="register.php" class="btn btn-secondary">Start Selling</a>
        <?php endif; ?>
    </div>
</section>

<div class="container">
    <h2 class="page-title">Latest Listings</h2>

    <?php if (count($listings) > 0): ?>
        <div class="listings-grid">
            <?php foreach ($listings as $listing): ?>
                <a href="listing-detail.php?id=<?= $listing['id'] ?>">
                    <div class="listing-card">
                        <?php if ($listing['image']): ?>
                            <img src="assets/images/<?= htmlspecialchars($listing['image']) ?>" alt="<?= htmlspecialchars($listing['title']) ?>">
                        <?php else: ?>
                            <img src="assets/images/placeholder.png" alt="No image">
                        <?php endif; ?>
                        <div class="listing-card-body">
                            <h3><?= htmlspecialchars($listing['title']) ?></h3>
                            <p class="price">R<?= number_format($listing['price'], 2) ?></p>
                            <p class="category"><?= htmlspecialchars($listing['category']) ?></p>
                            <p class="seller">Sold by: <?= htmlspecialchars($listing['seller_name']) ?></p>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>No listings yet. <a href="register.php">Register</a> and be the first to sell something!</p>
    <?php endif; ?>
</div>

<footer>
    <p>&copy; 2026 ReTrade. All rights reserved.</p>
</footer>

<script src="/c2c-retrade/assets/js/main.js"></script>
</body>
</html>
