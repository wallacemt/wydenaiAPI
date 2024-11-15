<?php

$request_uri = strtok($_SERVER['REQUEST_URI'], '?');
$request_uri = rtrim($request_uri, '/');

switch ($request_uri) {
    case '/login':
        include '/api/login.php';
        break;
    
    case '/cadastro':
        include '/api/cadastro.php';
        break;
    
    case '/get_messages':
        include '/api/get_messages.php';
        break;
    
    default:
        echo "Bem-vindo ao chat com IA!";
        break;
}

?>
