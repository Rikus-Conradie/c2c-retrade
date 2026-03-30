<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

requireLogin();

$user_id = $_SESSION['user_id'];

if (!isset($_GET['id'])) {
    header("Location: profile.php");
    exit();
}

$id = $_GET['id'];

// Confirmation if this is the correct user
$stmt = $pdo->prepare("SELECT * FROM listings WHERE id = ? AND user_id = ?");
$stmt->execute([$id, $user_id]);
$listing = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$listing) {
    header("Location: profile.php");
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $price = trim($_POST['price']);
    $category = trim($_POST['category']);

    if (empty($title) || empty($description) || empty($price) || empty($category)) {
        $error = "Please fill in all fields.";
    } else {
        $image = $listing['image'];

        // Edit Picture
        if (!empty($_FILES['image']['name'])) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

            if (!in_array($ext, $allowed)) {
                $error = "Only JPG, PNG and GIF images are allowed.";
            } else {
                $image = uniqid() . '.' . $ext;
                move_uploaded_file($_FILES['image']['tmp_name'], __DIR__ . '/assets/images/' . $image);
            }
        }

        if (!$error) {
            // Update the listing
            $stmt = $pdo->prepare("UPDATE listings SET title=?, description=?, price=?, category=?, image=? WHERE id=? AND user_id=?");
            $stmt->execute([$title, $description, $price, $category, $image, $id, $user_id]);
            $success = "Listing updated successfully!";

            
            $stmt = $pdo->prepare("SELECT * FROM listings WHERE id = ?");
            $stmt->execute([$id]);
            $listing = $stmt->fetch(PDO::FETCH_ASSOC);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Listing - ReTrade</title>
    <link rel="stylesheet" href="/c2c-retrade/assets/css/style.css">
</head>
<body>

<?php require_once __DIR__ . '/includes/header.php'; ?>

<div class="form-container" style="max-width:600px;">
    <h2>Edit Listing</h2>

    <?php if ($error): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="POST" action="" enctype="multipart/form-data">
        <div class="form-group">
            <label>Title</label>
            <input type="text" name="title" value="<?= htmlspecialchars($listing['title']) ?>" required>
        </div>
        <div class="form-group">
            <label>Description</label>
            <textarea name="description" rows="5" required><?= htmlspecialchars($listing['description']) ?></textarea>
        </div>
        <div class="form-group">
            <label>Price (R)</label>
            <input type="number" name="price" value="<?= htmlspecialchars($listing['price']) ?>" step="0.01" min="0" required>
        </div>
        <div class="form-group">
            <label>Category</label>
            <select name="category" required>
                <option value="">Select a category</option>
                <?php foreach (['Electronics','Clothing','Furniture','Textbooks','Sports','Other'] as $cat): ?>
                    <option value="<?= $cat ?>" <?= $listing['category'] === $cat ? 'selected' : '' ?>>
                        <?= $cat ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>New Image (optional)</label>
            <input type="file" name="image" accept="image/*">
            <?php if ($listing['image']): ?>
                <p style="font-size:13px; color:#888; margin-top:5px;">Current image will be kept if no new one is uploaded.</p>
            <?php endif; ?>
        </div>
        <button type="submit" class="btn btn-primary" style="width:100%">Update Listing</button>
        <a href="profile.php" style="display:block; text-align:center; margin-top:15px; color:#888;">Cancel</a>
    </form>
</div>

<footer>
    <p>&copy; 2026 ReTrade. All rights reserved.</p>
</footer>

<script src="/c2c-retrade/assets/js/main.js"></script>
</body>
</html>