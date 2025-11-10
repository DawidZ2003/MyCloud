<?php
declare(strict_types=1);
session_start();

$homeDir = $_SESSION['home_dir'];
if (!isset($_GET['file'])) exit();

$relativeFile = $_GET['file'];
$filePath = realpath($homeDir . DIRECTORY_SEPARATOR . $relativeFile);

// Sprawdzenie, czy plik znajduje się w katalogu użytkownika
if ($filePath && str_starts_with($filePath, $homeDir) && is_file($filePath)) {
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="'.basename($filePath).'"');
    header('Content-Length: ' . filesize($filePath));
    readfile($filePath);
    exit();
}