<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<nav class="navbar">
    <div class="nav-container">
        <a href="/c2c-retrade/index.php" class="logo">Re<span>Trade</span></a>
        <div class="hamburger" onclick="toggleMenu()"><span></span><span></span><span></span></div>

        <ul class="nav-links">
            <li><a href="/c2c-retrade/listings.php">Browse</a></li>

            <?php if (isLoggedIn()): ?>
                <li><a href="/c2c-retrade/create-listing.php">Sell</a></li>
                <li><a href="/c2c-retrade/orders.php">Orders</a></li>
                <li><a href="/c2c-retrade/reviews.php">Reviews</a></li>
                <li><a href="/c2c-retrade/profile.php">Profile</a></li>

                <?php if (isAdmin() || isModerator() || isSupport()): ?>
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
<script>function toggleMenu() { document.querySelector('.nav-links').classList.toggle('open'); }</script>
