<?php
$password = "yourpassword"; 
$dir = "ACHDB/";

if (!isset($_GET['pw']) || $_GET['pw'] !== $password) {
    die("Access denied.");
}

$games = [];
$totalSizeRaw = 0;

if (is_dir($dir)) {
    $files = scandir($dir);
    natsort($files);

    foreach ($files as $file) {
        if (pathinfo($file, PATHINFO_EXTENSION) == 'rar') {
            $dashPos = strpos($file, '-');
            if ($dashPos !== false) {
                $filePath = $dir . $file;
                $sizeInBytes = filesize($filePath);
                $totalSizeRaw += $sizeInBytes;

                $games[] = [
                    'appid' => substr($file, 0, $dashPos),
                    'name' => str_replace('.rar', '', substr($file, $dashPos + 1)),
                    'size' => round($sizeInBytes / 1024, 2) . " KB",
                    'file' => $file
                ];
            }
        }
    }

    $output = [
        'stats' => [
            'count' => count($games),
            'last_update' => date("Y-m-d H:i:s"),
            'total_size' => round($totalSizeRaw / (1024 * 1024 * 1024), 2) . " GB"
        ],
        'games' => $games
    ];
    
    file_put_contents('cache.json', json_encode($output, JSON_UNESCAPED_UNICODE));
    echo "Done! Database updated.";
}
?>