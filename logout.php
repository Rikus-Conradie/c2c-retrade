<?php

// Logs the person out

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Remove all session data
session_unset();

// Destroy the session completely
session_destroy();

// Send them back to the home page
header("Location: index.php");
exit();
?>