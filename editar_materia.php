<?php
// Iniciar sessão
session_start();

// Verificar se é administrador
if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] === null) {
    header('Location: login.php');
    exit();
}

// Incluindo configurações do banco de dados
require_once 'includes/config.php';

// Obter ID da matéria
$questao_id = isset($_GET['questao_id']) ? (int)$_GET['questao_id'] : 0;

if ($questao_id <= 0) {
    die("ID da matéria inválido!");
}

// Verificar se a matéria existe
$sql_check_questao = "SELECT id, nome, descricao FROM questoes WHERE id = :id";
$stmt_check = $pdo->prepare($sql_check_questao);
$stmt_check->bindValue(':id', $questao_id, PDO::PARAM_INT);
$stmt_check->execute();

$questao = $stmt_check->fetch(PDO::FETCH_ASSOC);

if (!$questao) {
    die("Matéria não encontrada!");
}

// Processar edição
$mensagem = '';
$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    
    if (!empty($nome)) {
        // Atualizar a matéria
        $sql_update = "UPDATE questoes SET nome = :nome, descricao = :descricao WHERE id = :id";
        $stmt_update = $pdo->prepare($sql_update);
        $stmt_update->bindValue(':nome', $nome, PDO::PARAM_STR);
        $stmt_update->bindValue(':descricao', $descricao, PDO::PARAM_STR);
        $stmt_update->bindValue(':id', $questao_id, PDO::PARAM_INT);
        
        if ($stmt_update->execute()) {
            $mensagem = "Matéria '" . htmlspecialchars($nome) . "' atualizada com sucesso!";
            // Redirecionar para a página de questões
            header('Location: questoes.php');
            exit();
        } else {
            $erro = "Erro ao atualizar a matéria!";
        }
    } else {
        $erro = "Nome da matéria é obrigatório!";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Matéria - Sistema de Estudos</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        /* Header com imagem */
        .header {
            width: 100%;
            text-align: center;
            padding: 0;
            margin: 0;
            background-color: #2c3e50;
        }

        .header-image {
            width: 150px;
            height: 120px;
            object-fit: cover;
            object-position: center;
            border-radius: 0;
            margin: 0 auto 10px;
            border: none;
            padding: 0;
            display: block;
            box-sizing: border-box;
            max-width: 100%;
            height: auto;
        }

        .header-content {
            background-color: #2c3e50;
            color: white;
            padding: 10px 0;
            margin: 0;
            border-radius: 0 0 8px 8px;
            width: 100%;
        }

        .header-content h1 {
            margin: 0 0 5px 0;
            font-size: 1.3em;
            font-family: 'Crimson Text', Georgia, serif;
        }

        .header-content p {
            margin: 0;
            font-size: 0.9em;
        }

        .main-content {
            margin-top: 20px;
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .page-header {
            text-align: center;
            margin-bottom: 30px;
            padding: 20px;
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
            color: white;
            border-radius: 12px;
        }

        .page-header h1 {
            font-size: 2em;
            margin-bottom: 10px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #2c3e50;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1em;
        }

        .form-group textarea {
            min-height: 150px;
            resize: vertical;
        }

        .btn {
            display: inline-block;
            padding: 12px 25px;
            background-color: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            font-size: 1em;
            transition: background-color 0.3s;
            margin: 5px;
        }

        .btn:hover {
            background-color: #2980b9;
        }

        .btn-success {
            background-color: #27ae60;
        }

        .btn-success:hover {
            background-color: #219653;
        }

        .btn-secondary {
            background-color: #95a5a6;
        }

        .btn-secondary:hover {
            background-color: #7f8c8d;
        }

        .message {
            padding: 15px;
            margin: 15px 0;
            border-radius: 4px;
            text-align: center;
        }

        .message.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .message.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: #3498db;
            text-decoration: none;
            font-weight: bold;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            .container {
                padding: 10px;
            }
            
            .main-content {
                padding: 20px;
            }
            
            .page-header h1 {
                font-size: 1.5em;
            }
        }
    </style>
</head>
<body>
    <!-- Header com imagem -->
    <div class="header">
        <div style="text-align: center; padding: 10px 0;">
            <img src="uploads/images.jpg" alt="Logo do Grupo Nofap" class="header-image" onerror="this.style.display='none';">
            <div class="header-content">
                <h1>Sistema de Estudos</h1>
                <p>Editar Matéria</p>
            </div>
        </div>
    </div>
    
    <div class="container">
        <div class="main-content">
            <a href="questoes.php" class="back-link">← Voltar para Matérias</a>
            
            <div class="page-header">
                <h1>✏️ Editar Matéria</h1>
                <p>Atualize as informações da matéria</p>
            </div>
            
            <?php if ($erro): ?>
                <div class="message error">
                    <?php echo htmlspecialchars($erro); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($mensagem): ?>
                <div class="message success">
                    <?php echo htmlspecialchars($mensagem); ?>
                </div>
            <?php endif; ?>
            
            <form method="post" action="">
                <div class="form-group">
                    <label for="nome">Nome da Matéria:</label>
                    <input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($questao['nome']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="descricao">Descrição (opcional):</label>
                    <textarea id="descricao" name="descricao" placeholder="Breve descrição da matéria..."><?php echo htmlspecialchars($questao['descricao']); ?></textarea>
                </div>
                
                <div style="text-align: center; margin-top: 30px;">
                    <button type="submit" class="btn btn-success">Atualizar Matéria</button>
                    <a href="questoes.php" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>