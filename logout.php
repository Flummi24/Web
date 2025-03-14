<?php
session_start();

// Alle Session-Daten löschen
session_unset();

// Session zerstören
session_destroy();

// Weiterleitung zur Startseite (index.php)
header("Location: login");
exit();
?>
