<?php
// This file is the top section that appears on every page of the site

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
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

<nav class="navbar">
    <div class="nav-container">
        <a href="/c2c-retrade/index.php" class="logo">Re<span>Trade</span></a>

        <ul class="nav-links">
            <li><a href="/c2c-retrade/listings.php">Browse</a></li>

            <?php if (isLoggedIn()): ?>
                <li><a href="/c2c-retrade/create-listing.php">Sell</a></li>
                <li><a href="/c2c-retrade/orders.php">Orders</a></li>
                <li><a href="/c2c-retrade/reviews.php">Reviews</a></li>
                <li><a href="/c2c-retrade/profile.php">Profile</a></li>

                <?php if (isAdmin() || isModerator() || isSupport()): ?>
                    <!-- This link only shows for admin roles -->
                    <li><a href="/c2c-retrade/admin/index.php">Admin</a></li>
                <?php endif; ?>

                <li><a href="/c2c-retrade/logout.php">Logout</a></li>
            <?php else: ?>
                <li><a href="/c2c-retrade/login.php">Login</a></li>
                <li><a href="/c2c-retrade/register.php">Register</a></li>
            <?php endif; ?>
        </ul>
    </div>
</nav>