<?php
header("Access-Control-Allow-Origin: https://wydenai-client.vercel.app");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}
require_once __DIR__ . '/../vendor/autoload.php';

require_once __DIR__ . '/../vendor/autoload.php';  
require_once __DIR__ . '/db_connect.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);
$chat_id = $data['chat_id'];
$message = $data['message'];
$sender = $data['sender'];  

$headers = getallheaders();
$chatToken = isset($headers['Authorization']) ? str_replace('Bearer ', '', $headers['Authorization']) : null;

if (!$chatToken) {
    echo json_encode(['status' => 'error', 'message' => 'Token não fornecido']);
    http_response_code(400);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT * FROM chats WHERE id = ? AND chat_token = ?");
    $stmt->execute([$chat_id, $chatToken]);
    $chat = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$chat) {
        echo json_encode(['status' => 'error', 'message' => 'Chat não encontrado ou token inválido']);
        http_response_code(404);
        exit;
    }

    $stmt = $pdo->prepare("INSERT INTO messages (chat_id, sender, message, timestamp) VALUES (?, ?, ?, NOW())");
    $stmt->execute([$chat_id, $sender, $message]);

    echo json_encode([
        'status' => 'success',
        'message' => 'Mensagem enviada com sucesso!'
    ]);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    http_response_code(500);
    exit;
}
