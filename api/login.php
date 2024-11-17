<?php
// Cabeçalhos para permitir CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");


require_once __DIR__ . '/../vendor/autoload.php';  // Caminho absoluto baseado na localização do script


$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
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

        // Buscar o usuario pelo email
        $stmt = $pdo->prepare("SELECT id, password, curso, nome FROM usuarios WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $chatToken = bin2hex(random_bytes(16));
            echo json_encode([
                'id' => $user['id'],
                'curso' => $user['curso'],
                'nome'=> $user['nome'],
                'chatToken' => $chatToken,
            ]);
        } else {
            echo json_encode(['message' => 'Email ou senha invalidos']);
        }
    } else {
        echo json_encode(['message' => 'Dados incompletos']);
    }
} else {
    echo json_encode(['message' => 'Método não suportado']);
}
