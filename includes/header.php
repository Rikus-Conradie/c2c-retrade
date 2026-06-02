<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<nav class="navbar">
    <div class="nav-container">
        <a href="/index.php" class="logo">Re<span>Trade</span></a>
        <div class="hamburger" onclick="toggleMenu()"><span></span><span></span><span></span></div>

        <ul class="nav-links">
            <li><a href="/listings.php">Browse</a></li>

            <?php if (isLoggedIn()): ?>
                <li><a href="/create-listing.php">Sell</a></li>
                <li><a href="/orders.php">Orders</a></li>
                <li><a href="/reviews.php">Reviews</a></li>
                <li><a href="/profile.php">Profile</a></li>

                <?php if (isAdmin() || isModerator() || isSupport()): ?>
                    <li><a href="/admin/index.php">Admin</a></li>
                <?php endif; ?>

                <li><a href="/logout.php">Logout</a></li>
            <?php else: ?>
                <li><a href="/login.php">Login</a></li>
                <li><a href="/register.php">Register</a></li>
            <?php endif; ?>
        </ul>
    </div>
</nav>
<script>function toggleMenu() { document.querySelector('.nav-links').classList.toggle('open'); }</script>
