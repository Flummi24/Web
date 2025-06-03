<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login");
    exit();
}

require_once "db.php";

// Alle Programme laden (nicht nur 3)
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Verbindung fehlgeschlagen: " . $conn->connect_error);
}

$sql = "SELECT titel, name, bild FROM dateien ORDER BY hochgeladen_am DESC";
$result = $conn->query($sql);

$programme = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $row['bild_base64'] = base64_encode($row['bild']);
        $programme[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Alle Programme</title>
    <link rel="icon" type="image/png" href="favicon.png" />
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 40px;
            background-color: #f7f7f7;
        }

        .programme-grid {
            display: flex;
            gap: 30px;
            flex-wrap: wrap;
            justify-content: center;
        }

        .programme-card {
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            width: 300px;
            padding: 20px;
            text-align: center;
            transition: transform 0.3s ease;
        }

        .programme-card:hover {
            transform: translateY(-6px);
        }

        .programme-image img {
            width: 100%;
            height: 200px;
            object-fit: contain;
            margin-bottom: 15px;
        }

        .programme-titel {
            font-size: 18px;
            font-weight: bold;
            color: #2c3e50;
        }

        .programme-name {
            font-size: 14px;
            color: #7f8c8d;
        }

        h2 {
            text-align: center;
            font-size: 35px;
            margin-bottom: 40px;
        }
    </style>
</head>
<body>
    <h2>Alle Programme</h2>
    <div class="programme-grid">
        <?php foreach ($programme as $prog): ?>
            <a href="download.php?file=<?php echo urlencode($prog['name']); ?>" class="programme-card" style="text-decoration: none; color: inherit;">
                <div class="programme-image">
                    <img src="data:image/jpeg;base64,<?php echo $prog['bild_base64']; ?>" alt="Bild" />
                </div>
                <div class="programme-titel"><?php echo htmlspecialchars($prog['titel']); ?></div>
                <div class="programme-name"><?php echo htmlspecialchars($prog['name']); ?></div>
            </a>
        <?php endforeach; ?>
    </div>
</body>
</html>
