<?php
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "UnityGame";

$userEmail = $_POST['email'] ?? $_GET['email'] ?? null;

if (!$userEmail) {
    echo json_encode(["error" => "Missing user email"]);
    exit;
}

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(["error" => "Connection failed: " . $conn->connect_error]);
    exit;
}

$stmt = $conn->prepare("SELECT creatureId, creatureName, creatureTraits, creatureMemories, creatureDescription, creatureInteraction, creatureSprite FROM Creatures WHERE UserEmail = ?");
$stmt->bind_param("s", $userEmail);
$stmt->execute();
$result = $stmt->get_result();

$creatures = [];

while ($row = $result->fetch_assoc()) {
    $creatures[] = $row;
}

echo json_encode($creatures);

$stmt->close();
$conn->close();
?>
