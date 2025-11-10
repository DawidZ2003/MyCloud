<?php
session_start();
// Sprawdzamy, czy użytkownik jest zalogowany
if (!isset($_SESSION['loggedin'])) {
    header('Location: logowanie.php');
    exit();
}
$homeDir = $_SESSION['home_dir'];
// Jeżeli current_dir nie podano, wróć do katalogu domowego
$currentDir = isset($_POST['current_dir']) ? $_POST['current_dir'] : $homeDir;
// Normalizacja ścieżki
$currentDir = realpath($currentDir);
// Zabezpieczenie – katalog nie może wychodzić poza katalog domowy
if ($currentDir === false || strpos($currentDir, realpath($homeDir)) !== 0) {
    die("Nieprawidłowy katalog!");
}
// Tworzenie katalogu, jeśli nie istnieje
if (!is_dir($currentDir)) {
    mkdir($currentDir, 0755, true);
}
// Pełna ścieżka do pliku
$targetFile = $currentDir . DIRECTORY_SEPARATOR . basename($_FILES["fileToUpload"]["name"]);
// Upload
if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $targetFile)) {
    // Ścieżka względna względem home_dir (do poprawnego przekierowania)
    $relativePath = str_replace(realpath($homeDir) . DIRECTORY_SEPARATOR, '', $currentDir);

    if ($relativePath === "" || $relativePath === $homeDir) {
        header('Location: index.php');
    } else {
        header('Location: index.php?dir=' . urlencode($relativePath));
    }
    exit();
} else {
    echo "Błąd przy uploadzie pliku.";
}
?>
