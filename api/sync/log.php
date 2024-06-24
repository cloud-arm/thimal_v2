<?php

function log_init($file_name, $content)
{
    // Directory path
    $directory = '../../log';

    // Create directory if it doesn't exist
    if (!file_exists($directory)) {
        mkdir($directory, 0777, true); // Creates the directory recursively with full permissions
    }

    // File name
    $file = $directory . '/' . $file_name . '.txt';

    file_put_contents($file, $content . PHP_EOL, FILE_APPEND | LOCK_EX);
}
