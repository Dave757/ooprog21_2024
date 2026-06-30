<?php
// Database connection (mysqli)
// Adjust these if your XAMPP MySQL credentials are different
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "election";

$conn = mysqli_connect($host, $user, $pass, $dbname);

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}
?>
