<?php
session_start();

// Überprüfen, ob der Benutzer angemeldet ist
if (!isset($_SESSION['username'])) {
    header("Location: login"); // Zurück zur Login-Seite, wenn nicht angemeldet
    exit();
}

$isAdmin = ($_SESSION['role'] === 'admin');
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="styles.css"> <!-- Dein CSS-File -->
</head>
<body>
    <header>
        <div class="nav-bar">
            <!-- Container für die Buttons -->
            <div class="nav-buttons-container">
                <!-- Wenn Admin, dann Verwaltung anzeigen -->
                <?php if ($isAdmin): ?>
                    <a href="verwaltung.php" class="admin-button">Verwaltung</a>
                <?php endif; ?>

                <a href="chat.php" class="category-button-1">Chat</a>
                <a href="proxy.php" class="category-button-2">Proxy</a>
                <a href="#" class="category-button-3">Kategorie 3</a>
            </div>

            <!-- Logout-Button ganz rechts -->
            <a href="logout.php" class="logout-button">Logout</a>
        </div>
    </header>

    <main>
        <h2>Willkommen, <?php echo $_SESSION['username']; ?>!</h2>
        <p>Hier ist dein Dashboard. Wähle eine Kategorie oder verwalte Benutzer, wenn du Admin bist.</p>
        <p>Deine rolle ist <?php echo $_SESSION['role']; ?></p>
    </main>
</body>
</html>
