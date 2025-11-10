<?php
declare(strict_types=1);
session_start();
// Sprawdzenie logowania
if (!isset($_SESSION['loggedin'])) {
    header('Location: logowanie.php');
    exit();
}
// Ścieżki
$homeDir = $_SESSION['home_dir'];
$currentDir = $homeDir;
// Obsługa katalogów podrzędnych
if (!empty($_GET['dir'])) {
    $subDir = basename($_GET['dir']); // zabezpieczenie przed ../
    $currentDir .= DIRECTORY_SEPARATOR . $subDir;
}
// Odczyt zawartości katalogu
$itemsList = [];
if (is_dir($currentDir)) {
    foreach (scandir($currentDir) as $item) {
        if ($item === '.' || $item === '..') continue;
        $fullPath = $currentDir . DIRECTORY_SEPARATOR . $item;
        $relativePath = ltrim(str_replace($homeDir, '', $fullPath), DIRECTORY_SEPARATOR);
        // Rozszerzenie pliku
        $extension = strtolower(pathinfo($item, PATHINFO_EXTENSION));

        $itemsList[] = [
            'name' => $item,
            'is_dir' => is_dir($fullPath),
            'relative_path' => $relativePath,
            'extension' => $extension
        ];
    }
}
// Funkcja pomocnicza
function isHomeDir(string $currentDir, string $homeDir): bool {
    return realpath($currentDir) === realpath($homeDir);
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Dysk użytkownika</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.24/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="twoj_css.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap5.min.js"></script>
    <script src="twoj_js.js"></script>
</head>
<body onload="myLoadHeader()">
<div id="myHeader"></div>
<main>
    <section class="sekcja1">
        <div class="container-fluid">
            <h2>
                Aktualny katalog: <?= htmlspecialchars(isHomeDir($currentDir, $homeDir) ? 'Katalog macierzysty' : basename($currentDir)) ?>
            </h2>
            <!-- Powrót do katalogu macierzystego -->
            <?php if (!isHomeDir($currentDir, $homeDir)): ?>
                <a href="index.php" class="btn btn-secondary mb-3">
                    <img src="media/menu_icons/level_up.png" alt="Powrót" style="width:20px; height:20px; margin-right:5px;">
                    Powrót do katalogu macierzystego
                </a>
            <?php endif; ?>
            <!-- Tworzenie katalogu tylko w katalogu głównym -->
            <?php if (isHomeDir($currentDir, $homeDir)): ?>
                <form method="POST" action="create_dir.php" class="mb-3">
                    <div class="input-group">
                        <input type="text" name="new_dir" class="form-control" placeholder="Nazwa nowego katalogu" required>
                        <button type="submit" class="btn btn-primary" title="Utwórz katalog">
                            <img src="media/menu_icons/folder.svg" alt="Folder" style="width:24px; height:24px;">
                        </button>
                    </div>
                </form>
            <?php endif; ?>
            <h2>Zawartość katalogu:</h2>
            <!-- Upload pliku -->
            <form action="upload.php" method="post" enctype="multipart/form-data" class="mb-3">
                <div class="input-group">
                    <input type="file" name="fileToUpload" class="form-control" required>
                    <input type="hidden" name="current_dir" value="<?= htmlspecialchars($currentDir) ?>">
                    <button type="submit" class="btn btn-success" title="Upload">
                        <img src="media/menu_icons/cloud-upload.svg" alt="Upload" style="width:24px; height:24px;">
                    </button>
                </div>
            </form>
            <?php if (empty($itemsList)): ?>
                <p>Brak plików i katalogów.</p>
            <?php else: ?>
            <ul class="list-group">
                <?php foreach ($itemsList as $item): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <?php
                        $filePath = $currentDir . DIRECTORY_SEPARATOR . $item['name'];
                        $fileUrl = 'download.php?file=' . urlencode($item['relative_path']);
                        $ext = $item['extension'];

                        if ($item['is_dir'] && isHomeDir($currentDir, $homeDir)): ?>
                            <a href="index.php?dir=<?= urlencode($item['name']) ?>">📁 <?= htmlspecialchars($item['name']) ?></a>
                        <?php elseif ($item['is_dir']): ?>
                            <span>📁 <?= htmlspecialchars($item['name']) ?></span>
                        <?php elseif (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])): ?>
                            <!-- Miniatura obrazka -->
                            <a href="<?= $fileUrl ?>" target="_blank">
                                <img src="<?= $fileUrl ?>" alt="<?= htmlspecialchars($item['name']) ?>" style="height:50px; margin-right:10px;">
                                <?= htmlspecialchars($item['name']) ?>
                            </a>
                        <?php elseif (in_array($ext, ['mp4', 'webm', 'ogg'])): ?>
                        <!-- Odtwarzacz wideo z linkiem -->
                        <div style="display:flex; align-items:center; gap:10px; margin-bottom:5px;">
                            <video width="150" controls style="max-height:150px;">
                                <source src="<?= $fileUrl ?>" type="video/<?= $ext ?>">
                                Twoja przeglądarka nie obsługuje tego formatu wideo.
                            </video>
                            <a href="<?= $fileUrl ?>" target="_blank"><?= htmlspecialchars($item['name']) ?></a>
                        </div>
                        <?php elseif (in_array($ext, ['mp3', 'wav', 'ogg'])): ?>
                        <!-- Odtwarzacz audio z linkiem -->
                        <div style="display:flex; align-items:center; gap:10px; margin-bottom:5px;">
                            <audio controls style="width:200px; max-height:50px;">
                                <source src="<?= $fileUrl ?>" type="audio/<?= $ext ?>">
                                Twoja przeglądarka nie obsługuje tego formatu audio.
                            </audio>
                            <a href="<?= $fileUrl ?>" target="_blank"><?= htmlspecialchars($item['name']) ?></a>
                        </div>
                        <?php else: ?>
                            <!-- Plik do pobrania -->
                            <a href="<?= $fileUrl ?>">📄 <?= htmlspecialchars($item['name']) ?></a>
                        <?php endif; ?>
                        <!-- Przycisk usuwania -->
                        <a href="delete.php?file=<?= urlencode($item['relative_path']) ?>&dir=<?= urlencode(str_replace(realpath($homeDir) . DIRECTORY_SEPARATOR, '', realpath($currentDir))) ?>" 
                        class="btn btn-danger btn-sm"
                        onclick="return confirm('Na pewno usunąć?')">
                            🗑️
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
            <?php endif; ?>
        </div>
    </section>
</main>
<?php require_once 'footer.php'; ?>
</body>
</html>
