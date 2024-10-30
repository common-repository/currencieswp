<?php
spl_autoload_register('dnolbon_autoload');

function dnolbon_autoload($class)
{
    $prefix = 'Dnolbon\\';
    $baseDir = __DIR__ . '/src/';

    if (strpos($class, $prefix) === false) {
        return;
    }

    $file = $baseDir . str_replace('\\', '/', $class) . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
}
