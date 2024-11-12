<?php
header('Content-Type: application/json');

// Conectando ao banco de dados (certifique-se de atualizar os dados do seu banco)
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

switch ($requestMethod) {
    case 'POST':
        // Obtendo os dados enviados pela requisição POST (via JSON)
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (isset($data['email']) && isset($data['nome']) && isset($data['password']) && isset($data['curso'])) {
            // Verificar se o e-mail já existe no banco de dados
            $email = $data['email'];
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            $count = $stmt->fetchColumn();
            
            if ($count > 0) {
                echo json_encode(['message' => 'E-mail já cadastrado']);
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
                echo json_encode(['message' => 'Usuário cadastrado com sucesso']);
            } else {
                echo json_encode(['message' => 'Erro ao cadastrar usuário']);
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
