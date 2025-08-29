<?php
$servername = "localhost";   // XAMPP runs MySQL on localhost
$username   = "root";        // default username in XAMPP
$password   = "";            // default password is empty
$dbname     = "canteen_db";  // your database name

// --- MySQLi Connection ---
$conn = new mysqli($servername, $username, $password, $dbname);

// Check MySQLi connection
if ($conn->connect_error) {
    die("❌ MySQLi Connection failed: " . $conn->connect_error);
}

// --- PDO Connection ---
try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    // Set PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("❌ PDO Connection failed: " . $e->getMessage());
}
?>
