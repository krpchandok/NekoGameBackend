<?php
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "UnityGame";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) die("1");

$name = $_POST["name"];
$traits = $_POST["traits"];
$memories = $_POST["memories"];
$description = $_POST["description"];
$sprite = $_POST["sprite"]; // Base64 encoded
$interaction = 0;

$query = "INSERT INTO Creatures (creatureName, creatureTraits, creatureMemories, creatureDescription, creatureInteraction, creatureSprite)
          VALUES (?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($query);
$stmt->bind_param("ssssis", $name, $traits, $memories, $description, $interaction, $sprite);

if ($stmt->execute()) {
    echo "success";
} else {
    echo "22"; // insert failed
}

$conn->close();
?>
