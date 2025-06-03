<?php
session_start();
if (!isset($_SESSION['username'])) {
    die("Nicht autorisiert.");
}

require_once "db.php";

if (!isset($_GET['file'])) {
    die("Keine Datei angegeben.");
}

$filename = $_GET['file'];

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Verbindung fehlgeschlagen: " . $conn->connect_error);
}

// Datei anhand des Namens abrufen – aus der Spalte `daten`
$stmt = $conn->prepare("SELECT name, daten FROM dateien WHERE name = ?");
$stmt->bind_param("s", $filename);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    die("Datei nicht gefunden.");
}

$stmt->bind_result($name, $data);
$stmt->fetch();

header("Content-Type: application/octet-stream");
header("Content-Disposition: attachment; filename=\"" . $name . "\"");
header("Content-Length: " . strlen($data));

echo $data;

$stmt->close();
$conn->close();
exit;
