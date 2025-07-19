<?php
$servername = "localhost";
$DbUsername = "root";
$DbPassword = "root";
$dbname = "UnityGame";

// Connect to the database
$conn = new mysqli($servername, $DbUsername, $DbPassword, $dbname);

if ($conn->connect_error) {
    die("1"); // DB connection failed
}

// Get POST data
$UserEmail = $_POST["email"];
$UserPassword = $_POST["password"]; // HASH password

// Build and run the query
$LoginUserQuery = "SELECT Username FROM Users WHERE UserEmail = '$UserEmail' AND Password = '$UserPassword'";
$LoginResult = $conn->query($LoginUserQuery);

if (!$LoginResult) {
    die("22"); // Query failed
}

if ($LoginResult->num_rows > 0) {
    // User found
    $row = $LoginResult->fetch_assoc();
    echo "success:" . $row["Username"];
} else {
    // Invalid credentials
    die("55"); // 55 = user has not registered
}

$conn->close();
?>
