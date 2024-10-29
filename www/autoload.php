<?php 

    $dirs = ['/engine/', '/../config/'];

    foreach ($dirs as $key => $dir) {
        $dir = __DIR__ . $dir;
        $files = scandir($dir);
        foreach ($files as $key => $file) {
            $path = $dir . $file;
            if (!is_file($path)) continue;
            if (pathinfo($path)['extension'] !== 'php') continue;
            include($path);
        }
    }