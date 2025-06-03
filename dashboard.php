<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login");
    exit();
}

$isAdmin = ($_SESSION['role'] === 'admin');

// Datenbankverbindung
require_once "db.php";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Verbindung fehlgeschlagen: " . $conn->connect_error);
}

// Neueste 3 Programme mit Bild laden
$sql = "SELECT titel, name, bild FROM dateien ORDER BY hochgeladen_am DESC LIMIT 3";
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
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Dashboard</title>
    <link rel="icon" type="image/png" href="favicon.png" />
    <style>
html, body {
    overflow-x: hidden;
    max-width: 100%;
}


body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    background-color: ;
}

/* Navigation-Bar */
.nav-bar {
    display: flex;
    justify-content: center; /* zentriert alle Kinder horizontal */
    align-items: center;
    background-color: #555;
    padding: 20px;
    position: relative;
}

.nav-buttons-container {
    display: flex;
    gap: 20px;
}

.nav-buttons-container a,
.admin-button {
    text-decoration: none;
    color: white;
    padding: 13px 30px;
    border-radius: 30px;
    font-size: 19px;
    transition: background-color 0.3s ease;
    display: inline-block;
}

.logout-button {
    background-color: #dc3545;
    position: absolute;
    right: 20px;
    top: 50%;
    transform: translateY(-50%);
    text-decoration: none;
    color: white;
    padding: 15px 40px;
    border-radius: 40px;
    font-size: 18px;
}

/* Kategorien Styling */
.category-button-1 {
    background-color: #007bff;
    font-size: 20px;
}

.category-button-2 {
    background-color: #007bff;
    font-size: 20px;
}

.category-button-3 {
    background-color: #007bff;
    font-size: 20px;
}

/* Hover-Effekt */
.category-button-1:hover {
    background-color: #0056b3;
}

.category-button-2:hover {
    background-color: #0056b3;
}

.category-button-3:hover {
    background-color: #0056b3;
}

/* Admin Button Styling */
.admin-button {
    background-color: #6c757d;
}

.admin-button:hover {
    background-color: #5a6268;
}

/* Logout-Button Styling */
.logout-button {
    background-color: #dc3545;
    position: absolute;
    right: 20px; /* Logout ganz rechts */
}

.logout-button:hover {
    background-color: #c82333;
}

/* Hauptinhalt */
main {
    margin-top: 50px;
    text-align: center;
    padding: 20px;
}

h2 {
    text-align: center;
}

    /* Programme-Bereich */
.programme-section {
    margin-top: 60px;
    text-align: center;
}

.programme-grid {
    display: flex;
    gap: 30px;
    justify-content: center;
    flex-wrap: wrap;
    margin: 0 auto;
    max-width: 1200px;
}

.programme-card {
    background-color:rgb(240, 240, 240);
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

.programme-name {
    font-size: 14px;
    color: #7f8c8d;
}

.programme-title {
    font-size: 28px;
    text-decoration: none;
    color: inherit;
}

.programme-link {
    font-size: 28px;
    text-decoration: none;
    color: inherit;
    cursor: pointer;
}

.programme-title-wrapper {
    width: 100%;
    padding: 0 5%;
    text-align: left;
    margin-bottom: 20px;
}

.programme-titel {
    font-size: 18px;
    font-weight: bold;
    margin: 10px 0;
    color: #2c3e50;
}

.welcome-title {
    font-size: 28px;
    font-weight: 800;
    color: #000;
    margin-bottom: 40px;
    text-align: center;
}
    </style>
</head>
<body>
    <div class="content-wrapper">
        <header>
            <div class="nav-bar">
                <div class="nav-buttons-container">
                    <?php if ($isAdmin): ?>
                        <a href="verwaltung.php" class="admin-button">Verwaltung</a>
                    <?php endif; ?>
                    <a href="chat.php" class="category-button-1">Chat</a>
                    <a href="proxy.php" class="category-button-2">Proxy</a>
                    <a href="upload.php" class="category-button-3">Upload</a>
                </div>
                <a href="logout.php" class="logout-button">Logout</a>
            </div>
        </header>

        <h2>Willkommen, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>

        <div class="programme-section">
            <div class="programme-title-wrapper">
                <h3 class="programme-title">
                  <a href="programme.php" class="programme-link">PROGRAMME &gt;</a>
                </h3>
            </div>
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
        </div>
    </div>
</body>
</html>
