<?php
// This is the browse page where users can see all active listings
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Fetch all active listings from the database
$stmt = $pdo->query("SELECT listings.*, users.name AS seller_name 
                     FROM listings 
                     JOIN users ON listings.user_id = users.id 
                     WHERE listings.status = 'active' 
                     ORDER BY listings.created_at DESC");
$listings = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Listings - ReTrade</title>
    <link rel="stylesheet" href="/c2c-retrade/assets/css/style.css">
</head>
<body>

<?php require_once __DIR__ . '/includes/header.php'; ?>

<div class="container">
    <h2 class="page-title">Browse Listings</h2>

    <!-- Search bar that filters cards in real time using main.js -->
    <input type="text" id="search-input" placeholder="Search listings..." 
           style="width:100%; padding:12px; margin-bottom:30px; border:1px solid #ddd; 
                  border-radius:6px; font-size:15px;">

    <?php if (count($listings) > 0): ?>
        <div class="listings-grid">
            <?php foreach ($listings as $listing): ?>
                <a href="listing-detail.php?id=<?= $listing['id'] ?>">
                    <div class="listing-card">
                        <?php if ($listing['image']): ?>
                            <img src="assets/images/<?= htmlspecialchars($listing['image']) ?>" 
                                 alt="<?= htmlspecialchars($listing['title']) ?>">
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
        <p>No listings available yet. <a href="register.php" style="color:#00b4d8;">Register</a> and be the first to sell something!</p>
    <?php endif; ?>
</div>

<footer>
    <p>&copy; 2026 ReTrade. All rights reserved.</p>
</footer>

<script src="/c2c-retrade/assets/js/main.js"></script>
</body>
</html>