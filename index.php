<?php
// index.php na raiz

// Verifica a URL e chama o script correspondente
if ($_SERVER['REQUEST_URI'] == '/login') {
    include 'api/login.php';
} elseif ($_SERVER['REQUEST_URI'] == '/cadastro') {
    include 'api/cadastro.php';
} else {
    // Rota padrão ou 404
    echo "Bem-vindo ao chat com IA!";
}
?>
