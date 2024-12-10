<?php
function scan_wordpress($url) {
    // Mengatur opsi untuk permintaan HTTP
    $options = [
        "http" => [
            "header" => "User -Agent: PHP\r\n"
        ]
    ];
    $context = stream_context_create($options);

    // Mengambil konten dari URL
    $response = @file_get_contents($url, false, $context);
    
    if ($response === FALSE) {
        echo "Error: Unable to access the URL.\n";
        return;
    }

    // Memeriksa versi WordPress
    if (preg_match('/<meta name="generator" content="WordPress (.*?)"/', $response, $matches)) {
        echo "WordPress Version: " . htmlspecialchars($matches[1]) . "<br>";
    } else {
        echo "This site does not appear to be a WordPress site.<br>";
        return;
    }

    // Mencari tema yang digunakan
    $theme_url = $url . '/wp-content/themes/';
    $themes_response = @file_get_contents($theme_url, false, $context);
    if ($themes_response !== FALSE) {
        echo "Themes Directory is accessible.<br>";
        preg_match_all('/href="([^"]+)"/', $themes_response, $theme_matches);
        foreach ($theme_matches[1] as $theme) {
            if (strpos($theme, '/wp-content/themes/') !== false) {
                echo "Theme found: " . htmlspecialchars(basename($theme)) . "<br>";
            }
        }
    } else {
        echo "Themes Directory is not accessible.<br>";
    }

    // Mencari plugin yang digunakan
    $plugins_url = $url . '/wp-content/plugins/';
    $plugins_response = @file_get_contents($plugins_url, false, $context);
    if ($plugins_response !== FALSE) {
        echo "Plugins Directory is accessible.<br>";
        preg_match_all('/href="([^"]+)"/', $plugins_response, $plugin_matches);
        foreach ($plugin_matches[1] as $plugin) {
            if (strpos($plugin, '/wp-content/plugins/') !== false) {
                echo "Plugin found: " . htmlspecialchars(basename($plugin)) . "<br>";
            }
        }
    } else {
        echo "Plugins Directory is not accessible.<br>";
    }
}

// Memeriksa apakah form telah disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['url'])) {
    $url = trim($_POST['url']);
    // Menambahkan http:// jika tidak ada
    if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
        $url = "http://" . $url;
    }
    scan_wordpress($url);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WordPress Scanner</title>
</head>
<body>
    <h1>WordPress Scanner</h1>
    <form method="post" action="">
        <label for="url">Enter WordPress Site URL:</label>
        <input type="text" id="url" name="url" required>
        <input type="submit" value="Scan">
    </form>
</body>
</html>
