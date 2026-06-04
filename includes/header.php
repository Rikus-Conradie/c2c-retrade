<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<nav class="navbar">
    <div class="nav-container">
        <a href="<?= BASE_URL ?>/index.php" class="logo">Re<span>Trade</span></a>
        <div class="hamburger" onclick="toggleMenu()"><span></span><span></span><span></span></div>

        <ul class="nav-links">
            <li><a href="<?= BASE_URL ?>/listings.php">Browse</a></li>

            <?php if (isLoggedIn()): ?>
                <li><a href="<?= BASE_URL ?>/create-listing.php">Sell</a></li>
                <li><a href="<?= BASE_URL ?>/orders.php">Orders</a></li>
                <li><a href="<?= BASE_URL ?>/reviews.php">Reviews</a></li>
                <li><a href="<?= BASE_URL ?>/profile.php">Profile</a></li>

                <?php if (isAdmin() || isModerator() || isSupport()): ?>
                    <li><a href="<?= BASE_URL ?>/admin/index.php">Admin</a></li>
                <?php endif; ?>

                <li><a href="<?= BASE_URL ?>/logout.php">Logout</a></li>
            <?php else: ?>
                <li><a href="<?= BASE_URL ?>/login.php">Login</a></li>
                <li><a href="<?= BASE_URL ?>/register.php">Register</a></li>
            <?php endif; ?>
        </ul>
    </div>
</nav>
<script>function toggleMenu() { document.querySelector('.nav-links').classList.toggle('open'); }</script>
