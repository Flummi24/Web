<?php
if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST["url"])) {
    $url = filter_var($_POST["url"], FILTER_VALIDATE_URL);
    if ($url) {
        // Hier setzen wir den Header, um sicherzustellen, dass wir die deutsche Seite laden
        $ch = curl_init($url);

        // Setze den Accept-Language Header auf "de" für Deutsch
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Accept-Language: de"
        ));

        // Wir setzen Optionen, um den HTML-Inhalt zu laden
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30); // Timeout verhindern

        $response = curl_exec($ch);

        if(curl_errno($ch)) {
            echo "Fehler: " . curl_error($ch);
        } else {
            echo $response;
        }

        curl_close($ch);
        exit;
    } else {
        $error = "Ungültige URL!";
    }
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP Proxy</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            text-align: center;
        }

        .container {
            background: white;
            width: 50%;
            margin: 50px auto;
            padding: 20px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        .input-field {
            padding: 10px;
            width: 80%;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin-bottom: 10px;
        }

        .submit-button {
            padding: 10px 15px;
            background-color: blue;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .submit-button:hover {
            background-color: darkblue;
        }
    </style>
</head>
<body>
    <main>
        <div class="container">
            <h2>PHP Proxy</h2>
            <p>Gib eine URL ein, um sie über den Proxy aufzurufen.</p>

            <?php if (isset($error)): ?>
                <p style="color: red;"><?php echo $error; ?></p>
            <?php endif; ?>

            <form method="post" class="proxy-form">
                <input type="text" name="url" value="https://" required class="input-field">
                <button type="submit" class="submit-button">Aufrufen</button>
            </br>
            <p>Die Proxy unterstützt kein Javascript</p>
            </form>
        </div>
    </main>
</body>
</html>
