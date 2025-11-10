<?php
session_start();
if (!isset($_SESSION['loggedin'])) { header('Location: logowanie.php'); exit(); }
$homeDir = $_SESSION['home_dir'];

if (!empty($_POST['new_dir'])) {
    $newDir = basename($_POST['new_dir']);
    $path = $homeDir . DIRECTORY_SEPARATOR . $newDir;
    if (!is_dir($path)) mkdir($path, 0777, true);
}

header('Location: index.php');
exit();
?>