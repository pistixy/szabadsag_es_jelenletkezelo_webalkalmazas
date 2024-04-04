<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Letolthetö fájlok</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="body-container">
    <div class="navbar">
        <?php include "nav-bar.php"; ?>
    </div>
    <div class="main-content">
        <div class="my-requests">
            <h1>Elérhető Beosztások</h1>
            <?php
            $dirPath = __DIR__ . '/Beosztasok'; // Base directory
            $currentDir = isset($_GET['dir']) ? $_GET['dir'] : '';

            // Function to list directories and files
            function listFolderFiles($dir) {
                $rootPath = realpath($_SERVER['DOCUMENT_ROOT'] . '/szabadsag_es_jelenletkezelo_webalkalmazas/');
                $fileInfos = new DirectoryIterator($dir);
                foreach ($fileInfos as $fileInfo) {
                    if ($fileInfo->isDot()) continue;
                    $filename = htmlspecialchars($fileInfo->getFilename());
                    $filePath = realpath($fileInfo->getPathname());

                    if ($fileInfo->isDir()) {
                        echo "<a href='?dir={$filePath}'>$filename</a><br>";
                    } else {
                        // Convert the file path to a web path by stripping the root path and replacing backslashes
                        $webPath = str_replace([$rootPath, '\\'], ['', '/'], $filePath);
                        echo "<a href='/szabadsag_es_jelenletkezelo_webalkalmazas/{$webPath}' target='_blank'>$filename</a><br>";
                    }
                }
            }



            // Check if a specific directory is selected, if not list base directory
            if ($currentDir && file_exists($currentDir)) {
                echo "<a href='letoltheto_fajlok.php'>Vissza</a><br>";
                listFolderFiles($currentDir);
            } elseif (file_exists($dirPath)) {
                listFolderFiles($dirPath);
            } else {
                echo "A 'Beosztasok' könyvtár nem található.";
            }
            ?>
        </div>
        <div class="footer-div">
            <?php include "footer.php"; ?>
        </div>
    </div>
</div>
</body>
</html>
