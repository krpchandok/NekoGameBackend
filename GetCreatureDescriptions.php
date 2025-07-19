<?php
header('Content-Type: application/json');

$host = 'localhost';
$user = 'root';
$password = 'root';
$database = 'UnityGame';

$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "Database connection failed"]);
    exit;
}

// Accept either GET or POST
$email = $_REQUEST['email'] ?? null;
$creatureId = $_REQUEST['creatureId'] ?? null;

if (!$email || !$creatureId) {
    http_response_code(400);
    echo json_encode(["error" => "Missing email or creatureId"]);
    exit;
}

// Query for the description
$stmt = $conn->prepare("SELECT creatureDescription FROM creatures WHERE creatureId = ? AND UserEmail = ?");
$stmt->bind_param("is", $creatureId, $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    http_response_code(404);
    echo json_encode(["error" => "Creature not found"]);
    exit;
}

$row = $result->fetch_assoc();
echo json_encode(["description" => $row['creatureDescription']]);

$stmt->close();
$conn->close();
?>
