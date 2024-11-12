<?php
header('Content-Type: application/json');

// Conectando ao banco de dados
require_once __DIR__ . '/../vendor/autoload.php';  // Caminho absoluto baseado na localização do script


$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$host = $_ENV['DB_HOST'];
$dbname = $_ENV['DB_NAME'];
$username = $_ENV['DB_USER'];
$password = $_ENV['DB_PASSWORD'];

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Connection failed: ' . $e->getMessage()]);
    exit();
}

$requestMethod = $_SERVER['REQUEST_METHOD'];

if ($requestMethod === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['email']) && isset($data['password'])) {
        $email = $data['email'];
        $password = $data['password'];

        // Buscar o usuário pelo email
        $stmt = $pdo->prepare("SELECT id, password FROM usuarios WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            // Gerar o chatToken
            $chatToken = bin2hex(random_bytes(16));

            // Retornar id e chatToken
            echo json_encode([
                'id' => $user['id'],
                'chatToken' => $chatToken
            ]);
        } else {
            echo json_encode(['message' => 'Email ou senha inválidos']);
        }
    } else {
        echo json_encode(['message' => 'Dados incompletos']);
    }
} else {
    echo json_encode(['message' => 'Método não suportado']);
}
