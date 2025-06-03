<?php
session_start();

if (isset($_SESSION['username'])) {
    header("Location: dashboard.php");
    exit();
}

include('db.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $input_username = $_POST['username'];
    $input_password = $_POST['password'];

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Verbindung fehlgeschlagen: " . $conn->connect_error);
    }

    $sql = "SELECT * FROM user WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $input_username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($input_password, $row['password'])) {
            if ($row['banned'] == 1) {
                header("Location: banned.php");
                exit();
            } else {
                $_SESSION['username'] = $input_username;
                $_SESSION['role'] = $row['role'];
                header("Location: dashboard.php");
                exit();
            }
        } else {
            $error = "Ungültiges Passwort.";
        }
    } else {
        $error = "Benutzername nicht gefunden.";
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="icon" type="image/png" href="favicon.png">
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

        .login-container {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            padding: 40px;
            width: 300px;
            text-align: center;
        }

        .login-container h2 {
            color: #2c3e50;
            font-size: 24px;
            margin-bottom: 20px;
        }

        .input-field {
            width: 90%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
            background-color: #f8f9fa;
        }

        .input-field:focus {
            outline: none;
            border-color: #3498db;
            background-color: #fff;
        }

        .login-btn {
            width: 100%;
            padding: 12px;
            background-color: #3498db;
            border: none;
            border-radius: 5px;
            font-size: 18px;
            color: white;
            cursor: pointer;
        }

        .login-btn:hover {
            background-color: #2980b9;
        }

        .error-message {
            color: #e74c3c;
            font-size: 14px;
            margin-top: 10px;
        }

        .footer-text {
            margin-top: 20px;
            font-size: 14px;
            color: #7f8c8d;
        }

        .footer-text a {
            color: #3498db;
            text-decoration: none;
        }

        .footer-text a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <div class="login-container">
        <h2>Willkommen zurück!</h2>
        <form method="POST" action="">
            <input type="text" name="username" class="input-field" placeholder="Benutzername" required>
            <input type="password" name="password" class="input-field" placeholder="Passwort" required>
            <?php if (isset($error)) { echo "<div class='error-message'>$error</div>"; } ?>
            <button type="submit" class="login-btn">Einloggen</button>
        </form>
        <div class="footer-text">
            <p>Noch kein Konto? Bitte frag einen Administrator um hilfe</p>
        </div>
    </div>

</body>
</html>
