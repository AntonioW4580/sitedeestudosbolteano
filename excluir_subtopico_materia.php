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

// Obter ID do subtópico
$topico_id = isset($_GET['topico_id']) ? (int)$_GET['topico_id'] : 0;

if ($topico_id <= 0) {
    die("ID do subtópico inválido!");
}

// Verificar se o subtópico existe
$sql_check_topico = "SELECT id, questao_id, nome FROM topicos_secundarios WHERE id = :id";
$stmt_check = $pdo->prepare($sql_check_topico);
$stmt_check->bindValue(':id', $topico_id, PDO::PARAM_INT);
$stmt_check->execute();

$topico = $stmt_check->fetch(PDO::FETCH_ASSOC);

if (!$topico) {
    die("Subtópico não encontrado!");
}

// Obter informações da matéria para breadcrumb
$sql_questao = "SELECT id, nome as questao_nome FROM questoes WHERE id = :questao_id";
$stmt_questao = $pdo->prepare($sql_questao);
$stmt_questao->bindValue(':questao_id', $topico['questao_id'], PDO::PARAM_INT);
$stmt_questao->execute();
$questao = $stmt_questao->fetch(PDO::FETCH_ASSOC);

// Processar exclusão
$mensagem = '';
$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Excluir o subtópico
    $sql_delete = "DELETE FROM topicos_secundarios WHERE id = :id";
    $stmt_delete = $pdo->prepare($sql_delete);
    $stmt_delete->bindValue(':id', $topico_id, PDO::PARAM_INT);
    
    if ($stmt_delete->execute()) {
        $mensagem = "Subtópico '" . htmlspecialchars($topico['nome']) . "' excluído com sucesso!";
        // Redirecionar para a página de subtópicos
        header("Location: topicos_questoes.php?questao_id=" . $topico['questao_id'] . "&mensagem=" . urlencode($mensagem));
        exit();
    } else {
        $erro = "Erro ao excluir o subtópico!";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Excluir Subtópico - Sistema de Estudos</title>
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
            background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
            color: white;
            border-radius: 12px;
        }

        .page-header h1 {
            font-size: 2em;
            margin-bottom: 10px;
        }

        .confirmation-box {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            text-align: center;
        }

        .confirmation-box h2 {
            color: #856404;
            margin-bottom: 15px;
        }

        .confirmation-box p {
            margin-bottom: 20px;
            font-size: 1.1em;
        }

        .topico-info {
            background-color: #f8f9fa;
            border-left: 4px solid #3498db;
            padding: 15px;
            margin: 20px 0;
            border-radius: 0 4px 4px 0;
        }

        .topico-info h3 {
            color: #2c3e50;
            margin-bottom: 10px;
        }

        .topico-info p {
            color: #7f8c8d;
        }

        .btn {
            display: inline-block;
            padding: 12px 25px;
            background-color: #e74c3c;
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
            background-color: #c0392b;
        }

        .btn-secondary {
            background-color: #95a5a6;
        }

        .btn-secondary:hover {
            background-color: #7f8c8d;
        }

        .btn-success {
            background-color: #27ae60;
        }

        .btn-success:hover {
            background-color: #219653;
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
                <p>Excluir Subtópico</p>
            </div>
        </div>
    </div>
    
    <div class="container">
        <div class="main-content">
            <a href="topicos_questoes.php?questao_id=<?php echo $topico['questao_id']; ?>" class="back-link">← Voltar para Subtópicos</a>
            
            <div class="page-header">
                <h1>🗑️ Excluir Subtópico</h1>
                <p>Confirme a exclusão do subtópico</p>
            </div>
            
            <?php if ($erro): ?>
                <div class="message error">
                    <?php echo htmlspecialchars($erro); ?>
                </div>
            <?php endif; ?>
            
            <div class="confirmation-box">
                <h2>⚠️ Atenção!</h2>
                <p>Você está prestes a excluir permanentemente este subtópico:</p>
                
                <div class="topico-info">
                    <h3><?php echo htmlspecialchars($topico['nome']); ?></h3>
                    <p>Esta ação não pode ser desfeita. Todos os posts associados também serão excluídos.</p>
                </div>
                
                <p>Tem certeza que deseja continuar?</p>
            </div>
            
            <form method="post" action="">
                <div style="text-align: center; margin-top: 30px;">
                    <button type="submit" class="btn">✅ Confirmar Exclusão</button>
                    <a href="topicos_questoes.php?questao_id=<?php echo $topico['questao_id']; ?>" class="btn btn-secondary">❌ Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>