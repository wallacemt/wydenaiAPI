<?php

$request_uri = strtok($_SERVER['REQUEST_URI'], '?');
$request_uri = rtrim($request_uri, '/');

// index.php
if ($_SERVER['REQUEST_URI'] == '/login') {
    include '/api/login.php';  
} elseif ($_SERVER['REQUEST_URI'] == '/cadastro') {
    include '/api/cadastro.php';  
} else {
    echo "Bem-vindo ao chat com IA!";
}

?>
