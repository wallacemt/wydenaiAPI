<?php
header("Access-Control-Allow-Origin: https://wydenai-client.vercel.app");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/../vendor/autoload.php';  
require_once __DIR__ . '/db_connect.php';

header('Content-Type: application/json');

$headers = getallheaders();
$chatToken = isset($headers['Authorization']) ? str_replace('Bearer ', '', $headers['Authorization']) : null;

if (!$chatToken) {
    echo json_encode(['status' => 'error', 'message' => 'Token não fornecido']);
    http_response_code(401);
    exit;
}

$requestBody = json_decode(file_get_contents('php://input'), true);
$chat_id = $requestBody['chat_id'] ?? null;

if (!$chat_id) {
    echo json_encode(['status' => 'error', 'message' => 'ID do chat não fornecido']);
    http_response_code(400);
    exit();
}

try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare("DELETE FROM messages WHERE chat_id = ?");
    $stmt->execute([$chat_id]);

    $stmt = $pdo->prepare("DELETE FROM chats WHERE id = ?");
    $stmt->execute([$chat_id]);

    if ($stmt->rowCount() > 0) {
        $pdo->commit();
        echo json_encode(['status' => 'success', 'message' => 'Chat e mensagens excluídos com sucesso']);
    } else {
        $pdo->rollBack();
        echo json_encode(['status' => 'error', 'message' => 'Chat não encontrado ou sem permissão']);
        http_response_code(403);
    }
} catch (PDOException $e) {
    $pdo->rollBack();
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    http_response_code(500);
    exit();
}
?>
