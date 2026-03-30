<?php
// This page allows users to leave a review after a completed order
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

requireLogin();

$user_id = $_SESSION['user_id'];
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = $_POST['order_id'];
    $rating = $_POST['rating'];
    $comment = trim($_POST['comment']);
    $reviewed_id = $_POST['reviewed_id'];

    // Make sure the order is completed before allowing a review
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ? AND status = 'completed' 
                           AND (buyer_id = ? OR seller_id = ?)");
    $stmt->execute([$order_id, $user_id, $user_id]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        $error = "You can only review completed orders.";
    } else {
        // Check if review already exists
        $stmt = $pdo->prepare("SELECT id FROM reviews WHERE order_id = ? AND reviewer_id = ?");
        $stmt->execute([$order_id, $user_id]);

        if ($stmt->fetch()) {
            $error = "You have already reviewed this order.";
        } else {
            $stmt = $pdo->prepare("INSERT INTO reviews (order_id, reviewer_id, reviewed_id, rating, comment) 
                                   VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$order_id, $user_id, $reviewed_id, $rating, $comment]);
            $success = "Review submitted successfully!";
        }
    }
}

$stmt = $pdo->prepare("SELECT orders.*, listings.title,
                        CASE 
                            WHEN orders.buyer_id = ? THEN sellers.name
                            ELSE buyers.name
                        END AS other_user_name,
                        CASE
                            WHEN orders.buyer_id = ? THEN orders.seller_id
                            ELSE orders.buyer_id
                        END AS other_user_id
                        FROM orders
                        JOIN listings ON orders.listing_id = listings.id
                        JOIN users AS buyers ON orders.buyer_id = buyers.id
                        JOIN users AS sellers ON orders.seller_id = sellers.id
                        WHERE orders.status = 'completed'
                        AND (orders.buyer_id = ? OR orders.seller_id = ?)");
$stmt->execute([$user_id, $user_id, $user_id, $user_id]);
$completed_orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch reviews received by this user
$stmt = $pdo->prepare("SELECT reviews.*, users.name AS reviewer_name 
                        FROM reviews 
                        JOIN users ON reviews.reviewer_id = users.id 
                        WHERE reviews.reviewed_id = ? 
                        ORDER BY reviews.created_at DESC");
$stmt->execute([$user_id]);
$received_reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reviews - ReTrade</title>
    <link rel="stylesheet" href="/c2c-retrade/assets/css/style.css">
    <style>
        .review-card {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            margin-bottom: 20px;
        }
        .stars { color: #f4a261; font-size: 20px; }
    </style>
</head>
<body>

<?php require_once __DIR__ . '/includes/header.php'; ?>

<div class="container">
    <h2 class="page-title">Reviews</h2>

    <?php if ($error): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <!-- Leave a review section -->
    <?php if (count($completed_orders) > 0): ?>
        <h3 style="margin-bottom:20px;">Leave a Review</h3>
        <?php foreach ($completed_orders as $order): ?>
            <div class="review-card">
                <p style="margin-bottom:10px;">
                    <strong><?= htmlspecialchars($order['title']) ?></strong> 
                    — Trade with <?= htmlspecialchars($order['other_user_name']) ?>
                </p>
                <form method="POST">
                    <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                    <input type="hidden" name="reviewed_id" value="<?= $order['other_user_id'] ?>">
                    <div class="form-group">
                        <label>Rating</label>
                        <select name="rating" required style="padding:8px; border-radius:4px; border:1px solid #ddd;">
                            <option value="5">5 stars - Excellent</option>
                            <option value="4">4 stars - Good</option>
                            <option value="3">3 stars - Average</option>
                            <option value="2">2 stars - Poor</option>
                            <option value="1">1 star - Terrible</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Comment</label>
                        <textarea name="comment" rows="3" 
                                  placeholder="Share your experience..." 
                                  style="width:100%; padding:10px; border:1px solid #ddd; border-radius:6px;"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Submit Review</button>
                </form>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- Reviews received -->
    <h3 style="margin:40px 0 20px;">Reviews I Have Received</h3>
    <?php if (count($received_reviews) > 0): ?>
        <?php foreach ($received_reviews as $review): ?>
            <div class="review-card">
                <p class="stars"><?= str_repeat('★', $review['rating']) . str_repeat('☆', 5 - $review['rating']) ?></p>
                <p style="margin:10px 0;"><?= htmlspecialchars($review['comment']) ?></p>
                <p style="color:#888; font-size:13px;">
                    By <?= htmlspecialchars($review['reviewer_name']) ?> 
                    on <?= date('d M Y', strtotime($review['created_at'])) ?>
                </p>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No reviews yet.</p>
    <?php endif; ?>
</div>

<footer>
    <p>&copy; 2026 ReTrade. All rights reserved.</p>
</footer>

<script src="/c2c-retrade/assets/js/main.js"></script>
</body>
</html>