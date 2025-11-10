<?php
declare(strict_types=1);
session_start(); 

if (!isset($_SESSION['loggedin'])) {
    header('Location: logowanie.php');
    exit();
}

$homeDir = $_SESSION['home_dir'];
$currentDir = $homeDir;

// jeśli w GET podano podkatalog
if (!empty($_GET['dir'])) {
    $subDir = basename($_GET['dir']); // zabezpieczenie
    $currentDir .= DIRECTORY_SEPARATOR . $subDir;
}

// odczyt zawartości katalogu
$itemsList = [];
if (is_dir($currentDir)) {
    $items = scandir($currentDir);
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') continue;
        $fullPath = $currentDir . DIRECTORY_SEPARATOR . $item;
        $itemsList[] = [
            'name' => $item,
            'is_dir' => is_dir($fullPath)
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Żurek - Dysk użytkownika</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.24/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" type="text/css" href="twoj_css.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript" language="javascript" src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap5.min.js"></script>
    <script type="text/javascript" src="twoj_js.js"></script>
</head>
<body onload="myLoadHeader()">
    <div id='myHeader'></div>
    <main>
        <section class="sekcja1">
            <div class="container-fluid">
                <h2>Aktualny katalog: <?= htmlspecialchars($currentDir === $homeDir ? 'Katalog macierzysty' : basename($currentDir)) ?></h2>
                <!-- Level Up -->
                <?php if ($currentDir !== $homeDir): ?>
                    <a href="index.php" class="btn btn-secondary mb-3">⬆ Powrót do katalogu macierzystego</a>
                <?php endif; ?>
                <!-- Tworzenie katalogu tylko w katalogu macierzystym -->
                <?php if ($currentDir === $homeDir): ?>
                    <form method="POST" action="create_dir.php" class="mb-3">
                        <div class="input-group">
                            <input type="text" name="new_dir" class="form-control" placeholder="Nazwa nowego katalogu" required>
                            <button type="submit" class="btn btn-primary">Utwórz katalog</button>
                        </div>
                    </form>
                <?php endif; ?>
                <h2>Zawartość katalogu:</h2>
                <?php if (empty($itemsList)): ?>
                    <p>Brak plików i katalogów.</p>
                <?php else: ?>
                    <ul class="list-group">
                        <?php foreach ($itemsList as $item): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <?php if ($item['is_dir'] && $currentDir === $homeDir): ?>
                                    <a href="index.php?dir=<?= urlencode($item['name']) ?>">📁 <?= htmlspecialchars($item['name']) ?></a>
                                <?php elseif ($item['is_dir']): ?>
                                    <span>📁 <?= htmlspecialchars($item['name']) ?></span>
                                <?php else: ?>
                                    <span>📄 <?= htmlspecialchars($item['name']) ?></span>
                                <?php endif; ?>
                                <a href="delete.php?file=<?= urlencode($item['name']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Na pewno usunąć?')">🗑️</a>
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
