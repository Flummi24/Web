<?php
session_start();
include('db.php'); // Lade die Variablen für die DB-Verbindung

if (!isset($_SESSION['username'])) {
    header("Location: login");
    exit();
}

// Stelle die Verbindung zur Datenbank her
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Verbindung fehlgeschlagen: " . $conn->connect_error);
}

// Nachricht absenden
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'])) {
    $username = $_SESSION['username']; // Benutzername aus der Session (angenommen, der Benutzer ist eingeloggt)
    $message = $_POST['message'];

    // Nachricht in die Datenbank einfügen
    $sql = "INSERT INTO chat (username, message) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username, $message);

    if ($stmt->execute()) {
        $message_status = "Nachricht gesendet!";
    } else {
        $message_status = "Fehler beim Senden der Nachricht!";
    }

    $stmt->close();
}

// Alle Nachrichten abfragen
$sql = "SELECT * FROM chat ORDER BY created_at DESC";
$result = $conn->query($sql);

$conn->close();
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center; /* Zentriert den Inhalt horizontal */
            align-items: center;     /* Zentriert den Inhalt vertikal */
            height: 100vh;           /* Vollständige Höhe des Viewports */
        }

        .chat-container {
            width: 100%;
            max-width: 800px;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            height: 90vh; /* Set height to 80% of the viewport */
        }

        h1 {
            text-align: left;
            color: #333;
            flex: 1; /* To ensure it takes available space */
        }

        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .messages {
            flex: 1; /* Take up available space */
            overflow-y: scroll; /* Enable scrolling */
            margin-bottom: 20px;
            padding-right: 10px;
            display: flex;
            flex-direction: column-reverse; /* Ensure new messages appear at the bottom */
        }

        .message {
            background-color: #f0f0f0;
            padding: 10px;
            margin: 5px 0;
            border-radius: 8px;
            display: inline-block;
        }

        .message span {
            font-weight: bold;
        }

        .message-text {
            margin-left: 5px;
        }

        .input-area {
            display: flex;
            margin-top: 10px;
        }

        input[type="text"] {
            flex: 1;
            padding: 15px;  /* Größeren Abstand innerhalb des Eingabefelds */
            margin-right: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 18px;  /* Größere Schriftgröße */
            height: 10px;  /* Höheres Eingabefeld */
            width: 220%;    /* Das Eingabefeld nimmt den verfügbaren Platz ein */
        }

        .send-button {
            padding: 10px 20px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 200px; /* Button breiter machen */
            }

.send-button:hover {
    background-color: #2980b9;
}

        .message-status {
            text-align: center;
            color: green;
            font-size: 14px;
            margin-top: 10px;
        }

        .dashboard-button {
            padding: 10px 20px;
            background-color: #2ecc71;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-align: center;
        }

        .dashboard-button:hover {
            background-color: #27ae60;
        }
    </style>
</head>
<body>

    <div class="chat-container">
        <div class="header-container">
            <h1>Chat</h1>
            <!-- Dashboard-Button rechts im Header -->
            <a href="dashboard.php"><button class="dashboard-button">Zum Dashboard</button></a>
        </div>

        <!-- Nachrichtenbereich -->
        <div class="messages">
            <?php
            // Zeige alle Nachrichten aus der DB an
            while ($row = $result->fetch_assoc()) {
                echo "<div class='message'><span>{$row['username']}:</span><span class='message-text'> {$row['message']}</span></div>";
            }
            ?>
        </div>

        <!-- Nachricht eingeben -->
        <div class="input-area">
            <form method="POST" action="">
                <input type="text" name="message" placeholder="Gib deine Nachricht ein..." required>
                <button class="send-button" type="submit">Senden</button>
            </form>
        </div>

        <?php
        // Nachricht Statusanzeige
        if (isset($message_status)) {
            echo "<div class='message-status'>$message_status</div>";
        }
        ?>
    </div>

</body>
</html>
