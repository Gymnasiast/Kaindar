<?php

use Cyndaron\DBConnection;

/**
 * Kaindar autoloader (PSR-4)
 */

spl_autoload_register(function ($class)
{
    // project-specific namespace prefix
    $prefix = 'Kaindar\\';

    // base directory for the namespace prefix
    $base_dir = __DIR__ . '/src/';

    // does the class use the namespace prefix?
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0)
    {
        // no, move to the next registered autoloader
        return;
    }

    // get the relative class name
    $relative_class = substr($class, $len);

    // replace the namespace prefix with the base directory, replace namespace
    // separators with directory separators in the relative class name, append
    // with .php
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    // if the file exists, require it
    if (file_exists($file))
    {
        require $file;
    }
});

/**
 * Cyndaron autoloader (PSR-4)
 */
spl_autoload_register(function ($class)
{
    // project-specific namespace prefix
    $prefix = 'Cyndaron\\';

    // base directory for the namespace prefix
    $base_dir = __DIR__ . '/vendor/Cyndaron/';

    // does the class use the namespace prefix?
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0)
    {
        // no, move to the next registered autoloader
        return;
    }

    // get the relative class name
    $relative_class = substr($class, $len);

    // replace the namespace prefix with the base directory, replace namespace
    // separators with directory separators in the relative class name, append
    // with .php
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    // if the file exists, require it
    if (file_exists($file))
    {
        require $file;
    }
});

{
    $dbHost = 'localhost';
    $dbName = 'kaindar';
    $dbUser = 'root';
    $dbPass = '';

    include "config.php";

    if (!DBConnection::connect($dbHost, $dbName, $dbUser, $dbPass))
    {
        die('Kon niet verbinden met de database!');
    }
}

$pagina = ltrim((string)parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

if ($pagina == '' || $pagina == '/' || !file_exists($pagina . '.php'))
    include 'home.php';
else
    include $pagina . ".php";