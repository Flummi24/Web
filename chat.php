<?php

session_start();
include('db.php');

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Verbindung fehlgeschlagen: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'])) {
    $username = $_SESSION['username'];
    $message = $_POST['message'];

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
    <link rel="icon" type="image/png" href="favicon.png">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;  
            height: 100vh;          
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
            height: 90vh;
        }

        h1 {
            text-align: left;
            color: #333;
            flex: 1;
        }

        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .messages {
            flex: 1;
            overflow-y: scroll;
            margin-bottom: 20px;
            padding-right: 10px;
            display: flex;
            flex-direction: column-reverse; 
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
            padding: 15px;
            margin-right: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 18px;  
            height: 10px;  
            width: 220%; 
        }

        .send-button {
            padding: 10px 20px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 200px; 
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
            <a href="dashboard.php"><button class="dashboard-button">Zum Dashboard</button></a>
        </div>

        <div class="messages">
            <?php
            while ($row = $result->fetch_assoc()) {
                echo "<div class='message'><span>{$row['username']}:</span><span class='message-text'> {$row['message']}</span></div>";
            }
            ?>
        </div>

        <div class="input-area">
            <form method="POST" action="">
                <input type="text" name="message" placeholder="Gib deine Nachricht ein..." required>
                <button class="send-button" type="submit">Senden</button>
            </form>
        </div>

        <?php
        if (isset($message_status)) {
            echo "<div class='message-status'>$message_status</div>";
        }
        ?>
    </div>

</body>
</html>
