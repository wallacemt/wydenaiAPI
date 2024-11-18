<?php
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}
require_once __DIR__ . '/../vendor/autoload.php';  
require_once __DIR__ . '/db_connect.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);
$user_id = $data['user_id'];
$title = isset($data['title']) ? $data['title'] : 'Chat sem título';


$headers = getallheaders();
$chatToken = isset($headers['Authorization']) ? str_replace('Bearer ', '', $headers['Authorization']) : null;

if (!$chatToken) {
    echo json_encode(['status' => 'error', 'message' => 'Token não fornecido']);
    http_response_code(400);
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO chats (user_id, chat_token, title, start_time) VALUES (?, ?, ?, NOW())");
    $stmt->execute([$user_id, $chatToken, $title]);
    $chat_id = $pdo->lastInsertId();

    echo json_encode([
        'status' => 'success',
        'chat_id' => $chat_id,
        'title' => $title, 
    ]);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    http_response_code(500);
    exit;
}
