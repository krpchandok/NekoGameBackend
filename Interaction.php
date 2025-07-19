<?php
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "UnityGame";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) die("1");

$creatureId = $_POST["id"];

$query = "UPDATE Creatures SET creatureInteraction = creatureInteraction + 1 WHERE creatureId = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $creatureId);

if ($stmt->execute()) {
    echo "success";
} else {
    echo "22";
}

$conn->close();
?>
