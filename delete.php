<?php
session_start();
// Sprawdzamy, czy użytkownik jest zalogowany
if (!isset($_SESSION['loggedin'])) { 
    header('Location: logowanie.php'); // jeśli nie, przekieruj do logowania
    exit(); 
}
// Pobieramy katalog domowy użytkownika
$homeDir = $_SESSION['home_dir'];
$fullHomeDir = realpath($homeDir);
if ($fullHomeDir === false) die("Błąd ścieżki.");
// Sprawdzamy, czy podano plik lub katalog do usunięcia
if (!empty($_GET['file'])) {
    $path = $homeDir . DIRECTORY_SEPARATOR . $_GET['file'];
    $target = realpath($path);
    // Sprawdzamy, czy plik/katalog znajduje się w katalogu domowym
    if ($target !== false && strpos($target, $fullHomeDir) !== 0) {
        die("Nieprawidłowa ścieżka.");
    }
    // Usuwamy katalog lub plik, jeśli istnieje
    if ($target !== false && is_dir($target)) {
        $items = scandir($target);
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') continue;
            unlink($target . DIRECTORY_SEPARATOR . $item);
        }
        rmdir($target);
    } elseif ($target !== false && is_file($target)) {
        unlink($target);
    }
}
// Obliczamy katalog, do którego wracamy po usunięciu
$returnDir = dirname($_GET['file']);
// Przekierowujemy użytkownika z powrotem do odpowiedniego katalogu
if ($returnDir === "." || $returnDir === "") {
    header('Location: index.php');
} else {
    header('Location: index.php?dir=' . urlencode($returnDir));
}
exit();
?>
