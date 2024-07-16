<?php
$servername = "localhost";
$username = "Database_Username_Here";
$password = "Database_Password_Here";
$dbname = "Database_Name_Here";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
