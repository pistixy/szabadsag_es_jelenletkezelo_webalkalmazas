<?php
include "session_check.php";
include "connect.php";
include "function_get_name.php";

if (!isset($_SESSION['logged'])) {
    header("Location: login_form.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Letolthetö fájlok</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Custom Styles */
        .content-container {
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .content-container h1 {
            font-size: 24px;
            color: #333;
            margin-bottom: 20px;
        }

        .current-folder {
            font-size: 16px;
            color: #666;
            margin-bottom: 20px;
        }

        .file-list a {
            display: block;
            padding: 10px;
            margin-bottom: 5px;
            background-color: #ffffff;
            border: 1px solid #ddd;
            border-radius: 5px;
            text-decoration: none;
            color: #007bff;
            transition: background-color 0.3s ease, border-color 0.3s ease;
        }

        .file-list a:hover {
            background-color: #e9ecef;
            border-color: #007bff;
        }

        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: #007bff;
            text-decoration: none;
            font-weight: bold;
        }

        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
<?php include "navigation_bar-top.php"; ?>
<div class="body-container">
    <div class="navbar" id="sidebar">
        <?php include "navigation_bar-side.php"; ?>
    </div>
    <div class="main-content" id="main-content">
        <div class="test_content">
            <div class="content-container">
                <h1>Elérhető Beosztások</h1>
                <div class="current-folder">
                    <?php
                    $dirPath = __DIR__ . '/Beosztasok'; // Base directory
                    $currentDir = isset($_GET['dir']) ? $_GET['dir'] : $dirPath;
                    $relativePath = str_replace(realpath($dirPath), 'Beosztasok', realpath($currentDir));
                    echo "Jelenlegi mappa: ", htmlspecialchars($relativePath);
                    ?>
                </div>
                <?php
                // Function to list directories and files
                function listFolderFiles($dir) {
                    $rootPath = realpath($_SERVER['DOCUMENT_ROOT'] . '/szabadsag_es_jelenletkezelo_webalkalmazas/');
                    $fileInfos = new DirectoryIterator($dir);
                    echo '<div class="file-list">';
                    foreach ($fileInfos as $fileInfo) {
                        if ($fileInfo->isDot()) continue;
                        $filename = htmlspecialchars($fileInfo->getFilename());
                        $filePath = realpath($fileInfo->getPathname());

                        if ($fileInfo->isDir()) {
                            echo "<a href='?dir={$filePath}'><strong>$filename</strong></a>";
                        } else {
                            // Convert the file path to a web path by stripping the root path and replacing backslashes
                            $webPath = str_replace([$rootPath, '\\'], ['', '/'], $filePath);
                            echo "<a href='/szabadsag_es_jelenletkezelo_webalkalmazas/{$webPath}' target='_blank'>$filename</a>";
                        }
                    }
                    echo '</div>';
                }

                // Check if a specific directory is selected, if not list base directory
                if ($currentDir && file_exists($currentDir)) {
                    echo "<a href='downloadable_files.php' class='back-link'>Vissza</a><br>";
                    listFolderFiles($currentDir);
                } elseif (file_exists($dirPath)) {
                    listFolderFiles($dirPath);
                } else {
                    echo "A 'Beosztasok' könyvtár nem található.";
                }
                ?>
            </div>
        </div>
        <div class="footer-div">
            <?php include "footer.php"; ?>
        </div>
    </div>
</div>
<script src="collapse.js"></script>
</body>
</html>
