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
require_once __DIR__ . '/db_connect.php';

header('Content-Type: application/json');

$requestMethod = $_SERVER['REQUEST_METHOD'];

$headers = getallheaders();
$chatToken = isset($headers['Authorization']) ? str_replace('Bearer ', '', $headers['Authorization']) : null;

if (!$chatToken) {
    echo json_encode(['status' => 'error', 'message' => 'Token não fornecido']);
    http_response_code(401);
    exit;
}

if(isset($_GET['chat_id'])) {
    $chat_id = $_GET['chat_id'];
}else{
    echo json_encode(['status' => 'error', 'message' => 'ID do chat não fornecido']);
    http_response_code(400);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT sender, message, timestamp FROM messages WHERE chat_id = ? ORDER BY timestamp ASC");
    $stmt->execute([$chat_id]);
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode(['status' => 'success', 'messages' => $messages]);
    
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    http_response_code(500);
    exit;
}
?>
