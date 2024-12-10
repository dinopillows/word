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
        echo "WordPress Version: " . $matches[1] . "\n";
    } else {
        echo "This site does not appear to be a WordPress site.\n";
        return;
    }

    // Mencari tema yang digunakan
    $theme_url = $url . '/wp-content/themes/';
    $themes_response = @file_get_contents($theme_url, false, $context);
    if ($themes_response !== FALSE) {
        echo "Themes Directory is accessible.\n";
        preg_match_all('/href="([^"]+)"/', $themes_response, $theme_matches);
        foreach ($theme_matches[1] as $theme) {
            if (strpos($theme, '/wp-content/themes/') !== false) {
                echo "Theme found: " . basename($theme) . "\n";
            }
        }
    } else {
        echo "Themes Directory is not accessible.\n";
    }

    // Mencari plugin yang digunakan
    $plugins_url = $url . '/wp-content/plugins/';
    $plugins_response = @file_get_contents($plugins_url, false, $context);
    if ($plugins_response !== FALSE) {
        echo "Plugins Directory is accessible.\n";
        preg_match_all('/href="([^"]+)"/', $plugins_response, $plugin_matches);
        foreach ($plugin_matches[1] as $plugin) {
            if (strpos($plugin, '/wp-content/plugins/') !== false) {
                echo "Plugin found: " . basename($plugin) . "\n";
            }
        }
    } else {
        echo "Plugins Directory is not accessible.\n";
    }
}

// Ganti dengan URL situs WordPress yang ingin Anda scan
$url = 'http://example.com'; // Ganti dengan URL yang sesuai
scan_wordpress($url);

?>
