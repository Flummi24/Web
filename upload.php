<?php
require 'db.php';
$meldung = "";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    die("Datenbankverbindung fehlgeschlagen: " . $e->getMessage());
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $titel = $_POST['titel'];
    $beschreibung = $_POST['beschreibung'];
    $datei = $_FILES['datei'];
    $bild = $_FILES['bild'];

    if ($datei['error'] === 0 && $bild['error'] === 0) {
        $dateiName = basename($datei['name']);
        $dateiTyp = $datei['type'];
        $dateiInhalt = file_get_contents($datei['tmp_name']);
        $bildInhalt = file_get_contents($bild['tmp_name']);

        $stmt = $pdo->prepare("INSERT INTO dateien (titel, beschreibung, name, typ, daten, bild)
                               VALUES (:titel, :beschreibung, :name, :typ, :daten, :bild)");
        $stmt->bindParam(':titel', $titel);
        $stmt->bindParam(':beschreibung', $beschreibung);
        $stmt->bindParam(':name', $dateiName);
        $stmt->bindParam(':typ', $dateiTyp);
        $stmt->bindParam(':daten', $dateiInhalt, PDO::PARAM_LOB);
        $stmt->bindParam(':bild', $bildInhalt, PDO::PARAM_LOB);

        if ($stmt->execute()) {
            $meldung = "Upload erfolgreich!";
        } else {
            $meldung = "Fehler beim Speichern in der Datenbank.";
        }
    } else {
        $meldung = "Fehler beim Hochladen der Dateien.";
    }
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Datei hochladen</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f0f4f8;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .upload-container {
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            padding: 40px 30px;
            width: 420px;
            text-align: center;
        }

        .upload-container h2 {
            color: #2c3e50;
            margin-bottom: 25px;
        }

        label {
            display: block;
            margin: 12px 0 5px;
            color: #34495e;
            font-weight: bold;
            text-align: left;
        }

        input[type="text"],
        textarea,
        input[type="file"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 15px;
            background-color: #f8f9fa;
        }

        input[type="submit"] {
            width: 105%;
            padding: 12px;
            background-color: #3498db;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            color: white;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #2980b9;
        }

        .meldung {
            margin-top: 15px;
            font-size: 14px;
            color: #2c3e50;
        }
    </style>
</head>
<body>
<div class="upload-container">
    <h2>Datei hochladen</h2>
    <form action="upload.php" method="post" enctype="multipart/form-data">
        <label for="titel">Titel:</label>
        <input type="text" name="titel" required>

        <label for="beschreibung">Beschreibung:</label>
        <textarea name="beschreibung" required></textarea>

        <label for="datei">Datei:</label>
        <input type="file" name="datei" required>

        <label for="bild">Bild (Vorschaubild):</label>
        <input type="file" name="bild" accept="image/*" required>

        <input type="submit" value="Hochladen">
    </form>

    <?php if (!empty($meldung)): ?>
        <div class="meldung"><?= htmlspecialchars($meldung) ?></div>
    <?php endif; ?>
</div>
</body>
</html>
