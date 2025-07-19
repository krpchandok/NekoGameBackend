<?php
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "root"; // or "" if your MAMP MySQL password is empty
$dbname = "UnityGame";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(["error" => "connection failed"]);
    exit();
}

$email = $_GET['email'];

$stmt = $conn->prepare("SELECT * FROM Users WHERE UserEmail = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $response = [
        "Email" => $row["UserEmail"],
        "Username" => $row["Username"],
        "Password" => $row["Password"],
        "creatureCount" => intval($row["creatureCount"])
    ];
    echo json_encode($response);
} else {
    echo json_encode(["error" => "user not found"]);
}

$stmt->close();
$conn->close();
?>
