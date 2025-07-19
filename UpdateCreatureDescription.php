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

// Step 1: Get form data
$email = $_POST['email'] ?? null;
$creatureId = $_POST['creatureId'] ?? null;

if (!$email || !$creatureId) {
    http_response_code(400);
    echo json_encode(["error" => "Missing email or creatureId"]);
    exit;
}

// Step 2: Get creature info
$stmt = $conn->prepare("SELECT creatureName, creatureTraits FROM creatures WHERE creatureId = ? AND UserEmail = ?");
$stmt->bind_param("is", $creatureId, $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    http_response_code(404);
    echo json_encode(["error" => "Creature not found"]);
    exit;
}

$row = $result->fetch_assoc();
$creature = [
    "id" => (int)$creatureId,
    "name" => $row['creatureName'],
    "traits" => json_decode($row['creatureTraits']),
];

// Step 3: Send to Node.js description generator
$ch = curl_init("http://localhost:3000/api/generateDescription");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(["creature" => $creature]));

$response = curl_exec($ch);
curl_close($ch);

$decoded = json_decode($response, true);
if (!isset($decoded['description'])) {
    http_response_code(500);
    echo json_encode(["error" => "No description returned"]);
    exit;
}

$description = $decoded['description'];

// Step 4: Update description in DB
$update = $conn->prepare("UPDATE creatures SET creatureDescription = ? WHERE creatureId = ? AND UserEmail = ?");
$update->bind_param("sis", $description, $creatureId, $email);
if ($update->execute()) {
    echo json_encode(["status" => "success", "description" => $description]);
} else {
    http_response_code(500);
    echo json_encode(["error" => "Failed to update database"]);
}

$update->close();
$conn->close();
?>
