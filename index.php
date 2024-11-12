<?php

$request_uri = strtok($_SERVER['REQUEST_URI'], '?');
$request_uri = rtrim($request_uri, '/');

if ($request_uri == '/login') {
    include 'api/login.php';
} elseif ($request_uri == '/register') {
    include 'api/cadastro.php';
} else {
    http_response_code(404);
    echo "Página não encontrada.";
}
?>
