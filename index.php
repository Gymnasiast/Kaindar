<?php


$pagina = $_GET['pagina'] ?? '';

if ($pagina == '' || $pagina == '/' || !file_exists($pagina . 'php'))
    include 'home.php';
else
    include $pagina . ".php";