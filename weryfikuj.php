<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="pl" lang="pl">
<HEAD>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</HEAD>
<BODY>
<?php
session_start();
// blokada brute-force
if (isset($_SESSION['register_block_time'])) {
    $diff = time() - $_SESSION['register_block_time'];
    if ($diff < 60) {
        $remain = 60 - $diff;
        echo "Logowanie zablokowane. Spróbuj za $remain sekund.";
        exit();
    } else {
        unset($_SESSION['register_block_time']);
    }
}
$user = htmlentities($_POST['user'], ENT_QUOTES, "UTF-8");
$pass = htmlentities($_POST['pass'], ENT_QUOTES, "UTF-8");
$link = mysqli_connect("127.0.0.1","dawzursz_z5", "Dawidek7003$", "dawzursz_z5");
mysqli_query($link, "SET NAMES 'utf8'");
$result = mysqli_query($link, "SELECT * FROM users WHERE username='$user'");
$rekord = mysqli_fetch_array($result);
// Uzytkownik nie istnieje
if(!$rekord)
{
    $_SESSION['register_block_time'] = time();
    mysqli_close($link);
    echo "Niepoprawny login lub hasło!";
}
else
{
    // Uzytkownik istnieje i sprawdzamy haslo
    if($rekord['password']==$pass)
    {
        $_SESSION['loggedin'] = true;
        mysqli_query($link, "INSERT INTO goscieportalu (username, success, attempt_time) VALUES ('$user', 1, NOW())");
        header('Location: index.php');
    }
    else
    // Haslo uzytkownika niepoprawne
    {
        $_SESSION['register_block_time'] = time();
        mysqli_query($link, "INSERT INTO goscieportalu (username, success, attempt_time) VALUES ('$user', 0, NOW())");
        mysqli_close($link);
        echo "Niepoprawny login lub hasło!";
    }
}
?>
</BODY>
</HTML>