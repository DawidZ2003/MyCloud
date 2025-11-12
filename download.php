<?php
declare(strict_types=1);
session_start();
// Pobranie katalogu domowego użytkownika z sesji
$homeDir = $_SESSION['home_dir'];
// Jeśli nie podano pliku do pobrania, zakończ skrypt
if (!isset($_GET['file'])) exit();
// Pobranie nazwy pliku i utworzenie pełnej ścieżki
$relativeFile = $_GET['file'];
$filePath = realpath($homeDir . DIRECTORY_SEPARATOR . $relativeFile);
// Sprawdzenie, czy plik istnieje, znajduje się w katalogu użytkownika i jest plikiem
if ($filePath && str_starts_with($filePath, $homeDir) && is_file($filePath)) {
    // Ustawienie nagłówków do pobrania pliku
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="'.basename($filePath).'"');
    header('Content-Length: ' . filesize($filePath));
    // Wysłanie pliku do użytkownika
    readfile($filePath);
    exit();
}
?>