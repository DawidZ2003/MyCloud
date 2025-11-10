<?php
session_start();
if (!isset($_SESSION['loggedin'])) { header('Location: logowanie.php'); exit(); }
$homeDir = $_SESSION['home_dir'];

if (!empty($_GET['file'])) {
    $target = $homeDir . DIRECTORY_SEPARATOR . basename($_GET['file']);
    if (is_dir($target)) {
        $items = scandir($target);
        foreach ($items as $item) {
            if ($item !== '.' && $item !== '..') unlink($target . DIRECTORY_SEPARATOR . $item);
        }
        rmdir($target);
    } elseif (is_file($target)) {
        unlink($target);
    }
}

header('Location: index.php');
exit();
?>