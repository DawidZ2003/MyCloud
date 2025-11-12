<?php
// Start sesji
session_start();
// Sprawdzenie logowania
if (!isset($_SESSION['loggedin'])) { 
    header('Location: logowanie.php'); 
    exit(); 
}
// Katalog domowy użytkownika
$homeDir = $_SESSION['home_dir'];
// Tworzenie nowego katalogu
if (!empty($_POST['new_dir'])) {
    $newDir = basename($_POST['new_dir']); // nazwa katalogu bez ścieżki
    $path = $homeDir . DIRECTORY_SEPARATOR . $newDir;
    if (!is_dir($path)) mkdir($path, 0777, true);
}
// Powrót na stronę główną
header('Location: index.php');
exit();
?>