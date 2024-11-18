<?php
header("Access-Control-Allow-Origin: https://wydenai-client.vercel.app/");
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

if (isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];  
} else {
    echo json_encode(['status' => 'error', 'message' => 'user_id não fornecido']);
    http_response_code(400);  
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT id, title FROM chats WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $chats = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['status' => 'success', 'chats' => $chats]);

} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    http_response_code(500);  
    exit;
}
?>
