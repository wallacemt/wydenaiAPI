<?php
    header('Content-Type: application/json');
    $requestMethod = $_SERVER['REQUEST_METHOD'];
    $requestUri = $_SERVER['REQUEST_URI'];
    
    switch ($requestMethod) {
        case 'GET':
            if (strpos($requestUri, '/') !== false) {
               echo 'Api Funcionando';
            }
            break;
        default:
            echo json_encode(['message' => 'Método não suportado']);
            break;
    }
?>