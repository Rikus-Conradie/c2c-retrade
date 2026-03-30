<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

requireLogin();

$user_id = $_SESSION['user_id'];

if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    // Make sure the listing belongs to this user before deleting
    $stmt = $pdo->prepare("DELETE FROM listings WHERE id = ? AND user_id = ?");
    $stmt->execute([$delete_id, $user_id]);
    header("Location: profile.php");
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch all listings by this user
$stmt = $pdo->prepare("SELECT * FROM listings WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$listings = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - ReTrade</title>
    <link rel="stylesheet" href="/c2c-retrade/assets/css/style.css">
</head>
<body>

<?php require_once __DIR__ . '/includes/header.php'; ?>

<div class="container">

    <div style="background:#fff; padding:30px; border-radius:10px; 
                box-shadow:0 2px 8px rgba(0,0,0,0.08); margin-bottom:40px;">
        <h2 style="color:#1a1a2e; margin-bottom:10px;"><?= htmlspecialchars($user['name']) ?></h2>
        <p style="color:#888;"><?= htmlspecialchars($user['email']) ?></p>
        <p style="margin-top:8px;">
            Role: <strong style="color:#00b4d8;"><?= ucfirst($user['role']) ?></strong>
        </p>
        <p style="color:#888; font-size:13px; margin-top:8px;">
            Member since <?= date('d M Y', strtotime($user['created_at'])) ?>
        </p>
    </div>

    <!-- User's Listings -->
    <h2 class="page-title">My Listings</h2>

    <?php if (count($listings) > 0): ?>
        <div class="listings-grid">
            <?php foreach ($listings as $listing): ?>
                <div class="listing-card">
                    <?php if ($listing['image']): ?>
                        <img src="/c2c-retrade/assets/images/<?= htmlspecialchars($listing['image']) ?>" 
                             alt="<?= htmlspecialchars($listing['title']) ?>">
                    <?php else: ?>
                        <div style="width:100%; height:160px; background:#eee; 
                                    display:flex; align-items:center; justify-content:center; 
                                    color:#888;">No image</div>
                    <?php endif; ?>
                    <div class="listing-card-body">
                        <h3><?= htmlspecialchars($listing['title']) ?></h3>
                        <p class="price">R<?= number_format($listing['price'], 2) ?></p>
                        <p class="category"><?= htmlspecialchars($listing['category']) ?></p>
                        <p style="margin-top:8px;">
                            Status: <strong><?= ucfirst($listing['status']) ?></strong>
                        </p>
                        <!-- Edit and Delete buttons -->
                        <div style="margin-top:12px; display:flex; gap:8px;">
                            <a href="edit-listing.php?id=<?= $listing['id'] ?>" 
                               class="btn btn-primary" style="padding:6px 14px; font-size:13px;">Edit</a>
                            <a href="profile.php?delete=<?= $listing['id'] ?>" 
                               class="btn btn-danger delete-btn" 
                               style="padding:6px 14px; font-size:13px;">Delete</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>You have no listings yet. <a href="create-listing.php" style="color:#00b4d8;">Create one now</a></p>
    <?php endif; ?>

</div>

<footer>
    <p>&copy; 2026 ReTrade. All rights reserved.</p>
</footer>

<script src="/c2c-retrade/assets/js/main.js"></script>
</body>
</html>