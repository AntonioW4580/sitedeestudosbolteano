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

// Obter ID da postagem
$post_id = isset($_GET['post_id']) ? (int)$_GET['post_id'] : 0;

if ($post_id <= 0) {
    die("ID da postagem inválido!");
}

// Verificar se a postagem existe
$sql_check_post = "SELECT id, topico_secundario_id, titulo, status FROM posts_questoes WHERE id = :id";
$stmt_check = $pdo->prepare($sql_check_post);
$stmt_check->bindValue(':id', $post_id, PDO::PARAM_INT);
$stmt_check->execute();

$post = $stmt_check->fetch(PDO::FETCH_ASSOC);

if (!$post) {
    die("Postagem não encontrada!");
}

// Obter informações do subtópico para redirecionamento
$sql_topico = "SELECT id, questao_id, nome as topico_nome FROM topicos_secundarios WHERE id = :topico_id";
$stmt_topico = $pdo->prepare($sql_topico);
$stmt_topico->bindValue(':topico_id', $post['topico_secundario_id'], PDO::PARAM_INT);
$stmt_topico->execute();
$topico = $stmt_topico->fetch(PDO::FETCH_ASSOC);

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
    // Excluir a postagem
    $sql_delete = "DELETE FROM posts_questoes WHERE id = :id";
    $stmt_delete = $pdo->prepare($sql_delete);
    $stmt_delete->bindValue(':id', $post_id, PDO::PARAM_INT);
    
    if ($stmt_delete->execute()) {
        $mensagem = "Postagem '" . htmlspecialchars($post['titulo']) . "' excluída com sucesso!";
        // Redirecionar para a página de posts
        header("Location: posts_questoes.php?topico_id=" . $post['topico_secundario_id'] . "&mensagem=" . urlencode($mensagem));
        exit();
    } else {
        $erro = "Erro ao excluir a postagem!";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Excluir Postagem - Sistema de Estudos</title>
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

        .post-info {
            background-color: #f8f9fa;
            border-left: 4px solid #3498db;
            padding: 15px;
            margin: 20px 0;
            border-radius: 0 4px 4px 0;
        }

        .post-info h3 {
            color: #2c3e50;
            margin-bottom: 10px;
        }

        .post-info p {
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
                <p>Excluir Postagem</p>
            </div>
        </div>
    </div>
    
    <div class="container">
        <div class="main-content">
            <a href="posts_questoes.php?topico_id=<?php echo $post['topico_secundario_id']; ?>" class="back-link">← Voltar para Posts</a>
            
            <div class="page-header">
                <h1>🗑️ Excluir Postagem</h1>
                <p>Confirme a exclusão da postagem</p>
            </div>
            
            <?php if ($erro): ?>
                <div class="message error">
                    <?php echo htmlspecialchars($erro); ?>
                </div>
            <?php endif; ?>
            
            <div class="confirmation-box">
                <h2>⚠️ Atenção!</h2>
                <p>Você está prestes a excluir permanentemente esta postagem:</p>
                
                <div class="post-info">
                    <h3><?php echo htmlspecialchars($post['titulo']); ?></h3>
                    <p>Esta ação não pode ser desfeita.</p>
                    <?php if ($post['status']): ?>
                        <p>Status atual: <strong><?php echo $post['status'] === 'acertou' ? '✅ Acertou' : '❌ Errou'; ?></strong></p>
                    <?php endif; ?>
                </div>
                
                <p>Tem certeza que deseja continuar?</p>
            </div>
            
            <form method="post" action="">
                <div style="text-align: center; margin-top: 30px;">
                    <button type="submit" class="btn">✅ Confirmar Exclusão</button>
                    <a href="posts_questoes.php?topico_id=<?php echo $post['topico_secundario_id']; ?>" class="btn btn-secondary">❌ Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>