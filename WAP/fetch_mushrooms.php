<?php
header("Content-Type: application/json"); //JavaScript Object Notation

//připojení k databázi

$host = 'xxx';
$db = 'xxx';
$user = 'xxx';
$pass = 'xxx';

// připojení k databázi pomocí .PHP a výše specifikovaných parametrů

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// filter kategorie, viz volba kategorie, např. jedlé
$category = isset($_GET['category']) ? $_GET['category'] : '';

// SQL DOTAZ pro výběr hub
$sql = "SELECT * FROM mushrooms";
if ($category) {
    $sql .= " WHERE category = ?";
}

//příprava k vykonání dotazu do databáze

$stmt = $conn->prepare($sql);
if ($category) {
    $stmt->bind_param("s", $category);
}
$stmt->execute();
$result = $stmt->get_result();

//zpracování výsledků a uložení do pole $mushrooms

$mushrooms = [];
while ($row = $result->fetch_assoc()) {
    $mushrooms[] = $row;
}

//výpis proměnné $mushrooms v JSON

echo json_encode($mushrooms);

//uzavření připojení k databázi

$stmt->close();
$conn->close();
?>
