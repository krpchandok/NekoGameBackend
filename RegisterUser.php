<?php
$servername = "localhost";
$DbUsername = "root";
$DbPassword = "root";
$dbname = "UnityGame";

$conn = new mysqli($servername, $DbUsername, $DbPassword, $dbname);
if ($conn->connect_error) {
    die("1"); // DB connection failed
}

// Read POST body
$creatureId = isset($_POST["creatureId"]) ? intval($_POST["creatureId"]) : 0;
if ($creatureId === 0) {
    die("2"); // Invalid creatureId
}

// Fetch creature info
$stmt = $conn->prepare("SELECT creatureName, creatureTraits, creatureInteraction FROM creatures WHERE creatureId = ?");
$stmt->bind_param("i", $creatureId);
$stmt->execute();
$result = $stmt->get_result();
$creature = $result->fetch_assoc();

if (!$creature) {
    die("3"); // Creature not found
}

// Prepare request to Gemini memory gen API
$payload = [
    "name" => $creature["creatureName"],
    "traits" => json_decode($creature["creatureTraits"], true),
    "interactivityScore" => (int)$creature["creatureInteraction"]
];

$ch = curl_init("http://localhost:3000/api/generateMemories");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
$response = curl_exec($ch);
curl_close($ch);

$data = json_decode($response, true);

// Check response and update DB
if (isset($data['creatureDescription']) && isset($data['memories'])) {
    $memoriesJson = json_encode(array_map(fn($m) => $m['content'], $data['memories']));
    $description = $conn->real_escape_string($data['creatureDescription']);

    $updateStmt = $conn->prepare("UPDATE creatures SET creatureDescription = ?, creatureMemories = ? WHERE creatureId = ?");
    $updateStmt->bind_param("ssi", $description, $memoriesJson, $creatureId);
    $updateStmt->execute();

    echo "success";
} else {
    echo "4"; // Memory generation failed
}

$conn->close();
?>
