<?php
$servername = "localhost";
$DbUsername = "root";
$DbPassword = "root";
$dbname = "UnityGame";

$conn = new mysqli($servername, $DbUsername, $DbPassword, $dbname);

if ($conn->connect_error) {
    die("1"); // DB connection failed
}

$UserEmail = $_POST["email"];
$Username = $_POST["username"];
$UserPassword = $_POST["password"]; // HASH password

// Prepare SQL query with column names
$RegisterUserQuery = "INSERT INTO Users (UserEmail, Username, Password) VALUES ('$UserEmail', '$Username', '$UserPassword')";

// Run the query
if ($conn->query($RegisterUserQuery) === TRUE) {
    echo "success";
} else {
    // You can log the actual error in a real app: $conn->error
    die("22"); // Insert failed
}

$conn->close();
?>
