<?php
// this is handeling everything with logins. This allows the user to go across pages without needing to login every time
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

//Checks if the person is already logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// is the person taht is logged in a Admin
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

// is that person a moderator
function isModerator() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'moderator';
}

// is that person support
function isSupport() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'support';
}


// This kicks a user out if they are not logged in
function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit();
    }
}

// This only allows Admin to access things. Kicks everybody else off
function requireAdmin() {
    if (!isAdmin()) {
        header("Location: ../index.php");
        exit();
    }
}
?>