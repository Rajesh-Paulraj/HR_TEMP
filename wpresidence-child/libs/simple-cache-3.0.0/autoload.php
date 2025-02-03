<?php

spl_autoload_register(function ($class) {
    // Namespace prefix for PhpSpreadsheet
    $prefix_php_spreadsheet = 'PhpOffice\\PhpSpreadsheet\\';
    // Namespace prefix for SimpleCache
    $prefix_simple_cache = 'Psr\\SimpleCache\\';

    // Base directories for the namespaces
    $base_dir_php_spreadsheet = __DIR__ . '/libs/PhpSpreadsheet/';
    $base_dir_simple_cache = __DIR__ . '/libs/psr/simple-cache/';

    // Check if class uses PhpSpreadsheet namespace
    $len_php_spreadsheet = strlen($prefix_php_spreadsheet);
    if (strncmp($prefix_php_spreadsheet, $class, $len_php_spreadsheet) === 0) {
        // Get the relative class name for PhpSpreadsheet
        $relative_class = substr($class, $len_php_spreadsheet);

        // Replace namespace separators with directory separators
        $file = $base_dir_php_spreadsheet . str_replace('\\', '/', $relative_class) . '.php';

        // If the file exists, require it
        if (file_exists($file)) {
            require_once $file;
        }
        return; // Exit after loading PhpSpreadsheet class
    }

    // Check if class uses SimpleCache namespace
    $len_simple_cache = strlen($prefix_simple_cache);
    if (strncmp($prefix_simple_cache, $class, $len_simple_cache) === 0) {
        // Get the relative class name for SimpleCache
        $relative_class = substr($class, $len_simple_cache);

        // Replace namespace separators with directory separators
        $file = $base_dir_simple_cache . str_replace('\\', '/', $relative_class) . '.php';

        // If the file exists, require it
        if (file_exists($file)) {
            require_once $file;
        }
    }
});
