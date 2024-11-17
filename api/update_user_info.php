<?php
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: GET, POST, PUT, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/../vendor/autoload.php';

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

header('Content-Type: application/json');

$headers = getallheaders();
$chatToken = isset($headers['Authorization']) ? str_replace('Bearer ', '', $headers['Authorization']) : null;

if (!$chatToken) {
    echo json_encode(['status' => 'error', 'message' => 'Token não fornecido']);
    http_response_code(401);
    exit;
}

$requestBody = json_decode(file_get_contents('php://input'), true);
$user_id = $requestBody['user_id'] ?? null;
$newEmail = $requestBody['email'] ?? null;
$newNome = $requestBody['nome'] ?? null;

if (!$user_id || (!$newEmail && !$newNome)) {
    echo json_encode(['status' => 'error', 'message' => 'ID do usuário ou dados para atualização não fornecidos']);
    http_response_code(400);
    exit();
}

try {
    $updateFields = [];
    $params = [];

    if ($newEmail) {
        $updateFields[] = 'email = ?';
        $params[] = $newEmail;
    }

    if ($newNome) {
        $updateFields[] = 'nome = ?';
        $params[] = $newNome;
    }

    $params[] = $user_id;

    $stmt = $pdo->prepare("UPDATE usuarios SET " . implode(', ', $updateFields) . " WHERE id = ?");
    $stmt->execute($params);

    if ($stmt->rowCount() > 0) {
        echo json_encode(['status' => 'success', 'message' => 'Dados do usuário atualizados com sucesso']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Usuário não encontrado ou sem permissão']);
        http_response_code(403);
    }
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    http_response_code(500);
    exit();
}
?>
