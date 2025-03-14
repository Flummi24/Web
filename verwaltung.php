<?php
session_start();

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

include('db.php');

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Verbindung fehlgeschlagen: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Benutzer hinzufügen
    if (isset($_POST['add_user'])) {
        $new_username = $_POST['username'];
        $new_password = $_POST['password'];
        $role = $_POST['role'];

        $check_sql = "SELECT * FROM user WHERE username = ?";
        $stmt_check = $conn->prepare($check_sql);
        $stmt_check->bind_param("s", $new_username);
        $stmt_check->execute();
        $result = $stmt_check->get_result();

        if ($result->num_rows > 0) {
            $message = "Benutzername bereits vergeben. Bitte wähle einen anderen.";
        } else {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO user (username, password, role) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sss", $new_username, $hashed_password, $role);

            if ($stmt->execute()) {
                $message = "Benutzer erfolgreich hinzugefügt!";
            } else {
                $message = "Fehler: " . $stmt->error;
            }

            $stmt->close();
        }

        $stmt_check->close();
    }

    // Benutzer löschen
    elseif (isset($_POST['delete_user'])) {
        $username_to_delete = $_POST['username_to_delete'];

        $sql = "DELETE FROM user WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username_to_delete);

        if ($stmt->execute()) {
            $message = "Benutzer erfolgreich gelöscht!";
        } else {
            $message = "Fehler: " . $stmt->error;
        }

        $stmt->close();
    }

    // Benutzer bannen
    elseif (isset($_POST['ban_user'])) {
        $username_to_ban = $_POST['username_to_ban'];

        $sql = "UPDATE user SET banned = 1 WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username_to_ban);

        if ($stmt->execute()) {
            $message = "Benutzer erfolgreich gebannt!";
        } else {
            $message = "Fehler: " . $stmt->error;
        }

        $stmt->close();
    }

    // Benutzer entbannen
    elseif (isset($_POST['unban_user'])) {
        $username_to_unban = $_POST['username_to_unban'];

        $sql = "UPDATE user SET banned = 0 WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username_to_unban);

        if ($stmt->execute()) {
            $message = "Benutzer erfolgreich entbannt!";
        } else {
            $message = "Fehler: " . $stmt->error;
        }

        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Benutzerverwaltung</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f4f8;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            width: 800px;
            padding: 40px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            min-height: 600px;
            box-sizing: border-box;
        }

        h1 {
            text-align: center;
            color: #2c3e50;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            font-weight: bold;
            margin-bottom: 5px;
            display: block;
        }

        input[type="text"], input[type="password"], select {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
            box-sizing: border-box;
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: #3498db;
            border: none;
            color: white;
            font-size: 18px;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #2980b9;
        }

        .message {
            margin-top: 20px;
            text-align: center;
            font-size: 16px;
        }

        .message.success {
            color: green;
        }

        .message.error {
            color: red;
        }

        .form-section {
            margin-bottom: 40px;
        }
    </style>
</head>
<body>

    <div class="container">
        <h1>Benutzerverwaltung</h1>

        <!-- Benutzer hinzufügen -->
        <div class="form-section">
            <h2>Neuen Benutzer hinzufügen</h2>
            <form method="post" action="">
                <div class="form-group">
                    <label for="username">Benutzername</label>
                    <input type="text" id="username" name="username" required>
                </div>

                <div class="form-group">
                    <label for="password">Passwort</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <div class="form-group">
                    <label for="role">Rolle</label>
                    <select id="role" name="role" required>
                        <option value="user">Benutzer</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>

                <button type="submit" name="add_user">Benutzer hinzufügen</button>
            </form>
        </div>

        <!-- Benutzer verwalten (Bannen, Entbannen, Löschen) -->
        <div class="form-section">
            <h2>Benutzer verwalten</h2>
            <form method="post" action="">
                <div class="form-group">
                    <label for="username_to_manage">Benutzername</label>
                    <input type="text" id="username_to_manage" name="username_to_manage" required>
                </div>

                <div class="form-group">
                    <label for="action">Aktion</label>
                    <select id="action" name="action" required>
                        <option value="ban_user">Bannen</option>
                        <option value="unban_user">Entbannen</option>
                        <option value="delete_user">Löschen</option>
                    </select>
                </div>

                <button type="submit" name="manage_user">Aktion durchführen</button>
            </form>
        </div>

        <!-- Nachricht anzeigen -->
        <?php if (isset($message)) { ?>
            <div class="message <?php echo strpos($message, 'Fehler') === false ? 'success' : 'error'; ?>">
                <?php echo $message; ?>
            </div>
        <?php } ?>
    </div>

</body>
</html>
