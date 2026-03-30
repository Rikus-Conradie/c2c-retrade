<?php
// We are creating this file to just connect to mySQL database

$host = "localhost";        // XAMPP will run MySQL locally
$dbname = "c2c_retrade";   // Create the Databse
$username = "root";         
$password = "";             

try { 
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>