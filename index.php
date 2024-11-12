<?php
// index.php na raiz

// Remove parâmetros de consulta e barras finais da URL para comparação precisa
$request_uri = strtok($_SERVER['REQUEST_URI'], '?');
$request_uri = rtrim($request_uri, '/');

// Verifica a URL e chama o script correspondente
if ($request_uri == '/login') {
    include 'api/login.php';
} elseif ($request_uri == '/cadastro') {
    include 'api/cadastro.php';
} else {
    // Rota padrão ou 404
    http_response_code(404);
    echo "Página não encontrada.";
}
?>
