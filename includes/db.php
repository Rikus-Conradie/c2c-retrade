<?php
if (strpos($_SERVER['HTTP_HOST'], 'localhost') !== false) {
    $host     = 'localhost';
    $dbname   = 'c2c_retrade';
    $username = 'root';
    $password = '';
    define('BASE_URL', '/c2c-retrade');
} else {
    $host     = 'sql102.infinityfree.com';
    $dbname   = 'if0_42079179_retrade';
    $username = 'if0_42079179';
    $password = '2vecih86g9I8Jd';
    define('BASE_URL', '');
}

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
