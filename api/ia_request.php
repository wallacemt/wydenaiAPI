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

use Dotenv\Dotenv;

header('Content-Type: application/json');

$headers = getallheaders();
$chatToken = isset($headers['Authorization']) ? str_replace('Bearer ', '', $headers['Authorization']) : null;

if (!$chatToken) {
    echo json_encode(['status' => 'error', 'message' => 'Token não fornecido']);
    http_response_code(400);
    exit;
}

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$apiKey = $_ENV['IA_KEY'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents("php://input"), true);
    $userName = $input['name'] ?? 'Usuário';
    $userCourse = $input['course'] ?? 'um curso não especificado';
    $userMessage = $input['userMessage'] ?? '';

    if (!$userMessage) {
        echo json_encode(['status' => 'error', 'message' => 'Mensagem vazia.']);
        exit;
    }

    $apiUrl = "https://api-inference.huggingface.co/models/meta-llama/Llama-3.2-1B";  
    $payload = [
        "inputs" => "Olá, você está conversando com um estudante chamado $userName que está estudando $userCourse. A pergunta é: $userMessage"
    ];

    try {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
        echo $response;

        if (curl_errno($ch)) {
            throw new Exception(curl_error($ch));
        }


        $decodedResponse = json_decode($response, true);
        curl_close($ch);
      

        $content =  $decodedResponse[0]['generated_text'] ?? null;

        if ($content) {
            echo json_encode(['status' => 'success', 'data' => $content]);
        } else {
            throw new Exception('Falha ao obter resposta da IA.');
        }

    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}
?>
