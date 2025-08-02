<?php
$host = "localhost";       // or 127.0.0.1
$user = "root";            // default XAMPP/WAMP username
$password = "";            // default no password
$database = "hms_db";    // your database name

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
