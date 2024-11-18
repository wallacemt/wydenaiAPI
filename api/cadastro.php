<?php
// Cabeçalhos para permitir CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

require_once __DIR__ . '/../vendor/autoload.php';  
require_once __DIR__ . '/db_connect.php';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Connection failed: ' . $e->getMessage()]);
    exit();
}

$requestMethod = $_SERVER['REQUEST_METHOD'];

switch ($requestMethod) {
    case 'POST':
        // Obtendo os dados enviados pela requisição POST (via JSON)
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (isset($data['email']) && isset($data['nome']) && isset($data['password']) && isset($data['curso'])) {
            // Verificar se o e-mail ja existe no banco de dados
            $email = $data['email'];
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            $count = $stmt->fetchColumn();
            
            if ($count > 0) {
                echo json_encode(['message' => 'E-mail ja cadastrado']);
                   
                exit();
            }

            // Preparando a query de inserção
            $nome = $data['nome'];
            $password = password_hash($data['password'], PASSWORD_DEFAULT);
            $curso = $data['curso'];
            
            // Evitar SQL Injection com prepared statements
            $stmt = $pdo->prepare("INSERT INTO usuarios (email, nome, password, curso) VALUES (:email, :nome, :password, :curso)");
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':nome', $nome);
            $stmt->bindParam(':password', $password);
            $stmt->bindParam(':curso', $curso);

            if ($stmt->execute()) {
                echo json_encode(['message' => 'Usuario cadastrado com sucesso']);
            } else {
                echo json_encode(['message' => 'Erro ao cadastrar usuario']);
            }
        } else {
            echo json_encode(['message' => 'Dados incompletos']);
        }
        break;
    
    default:
        echo json_encode(['message' => 'Método não suportado']);
        break;
}
?>
