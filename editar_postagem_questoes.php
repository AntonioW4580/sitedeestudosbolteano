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
$sql_check_post = "SELECT id, topico_secundario_id, titulo, conteudo, nivel_dificuldade, status FROM posts_questoes WHERE id = :id";
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

// Processar edição
$mensagem = '';
$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo'] ?? '');
    $conteudo = trim($_POST['conteudo'] ?? '');
    $nivel_dificuldade = trim($_POST['nivel_dificuldade'] ?? 'médio');
    $status = trim($_POST['status'] ?? null);
    
    if (!empty($titulo) && !empty($conteudo)) {
        // Atualizar a postagem
        $sql_update = "UPDATE posts_questoes SET titulo = :titulo, conteudo = :conteudo, nivel_dificuldade = :nivel_dificuldade, status = :status WHERE id = :id";
        $stmt_update = $pdo->prepare($sql_update);
        $stmt_update->bindValue(':titulo', $titulo, PDO::PARAM_STR);
        $stmt_update->bindValue(':conteudo', $conteudo, PDO::PARAM_STR);
        $stmt_update->bindValue(':nivel_dificuldade', $nivel_dificuldade, PDO::PARAM_STR);
        $stmt_update->bindValue(':status', $status, PDO::PARAM_STR);
        $stmt_update->bindValue(':id', $post_id, PDO::PARAM_INT);
        
        if ($stmt_update->execute()) {
            $mensagem = "Postagem '$titulo' atualizada com sucesso!";
            // Redirecionar para a página de posts
            header("Location: posts_questoes.php?topico_id=" . $post['topico_secundario_id']);
            exit();
        } else {
            $erro = "Erro ao atualizar a postagem!";
        }
    } else {
        $erro = "Título e conteúdo da postagem são obrigatórios!";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Postagem - Sistema de Estudos</title>
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
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1em;
        }

        .form-group textarea {
            min-height: 200px;
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
                <p>Editar Postagem</p>
            </div>
        </div>
    </div>
    
    <div class="container">
        <div class="main-content">
            <a href="posts_questoes.php?topico_id=<?php echo $post['topico_secundario_id']; ?>" class="back-link">← Voltar para Posts</a>
            
            <div class="page-header">
                <h1>✏️ Editar Postagem</h1>
                <p>Atualize as informações da postagem</p>
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
                    <label for="titulo">Título da Questão:</label>
                    <input type="text" id="titulo" name="titulo" value="<?php echo htmlspecialchars($post['titulo']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="nivel_dificuldade">Nível de Dificuldade:</label>
                    <select id="nivel_dificuldade" name="nivel_dificuldade">
                        <option value="fácil" <?php echo $post['nivel_dificuldade'] === 'fácil' ? 'selected' : ''; ?>>Fácil</option>
                        <option value="médio" <?php echo $post['nivel_dificuldade'] === 'médio' ? 'selected' : ''; ?>>Médio</option>
                        <option value="difícil" <?php echo $post['nivel_dificuldade'] === 'difícil' ? 'selected' : ''; ?>>Difícil</option>
                        <option value="muito difícil" <?php echo $post['nivel_dificuldade'] === 'muito difícil' ? 'selected' : ''; ?>>Muito Difícil</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="status">Status (opcional):</label>
                    <select id="status" name="status">
                        <option value="" <?php echo !$post['status'] ? 'selected' : ''; ?>>Sem status</option>
                        <option value="acertou" <?php echo $post['status'] === 'acertou' ? 'selected' : ''; ?>>Acertou</option>
                        <option value="errou" <?php echo $post['status'] === 'errou' ? 'selected' : ''; ?>>Errou</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="conteudo">Conteúdo da Questão:</label>
                    <textarea id="conteudo" name="conteudo" placeholder="Digite o conteúdo completo da questão..." required><?php echo htmlspecialchars($post['conteudo']); ?></textarea>
                </div>
                
                <div style="text-align: center; margin-top: 30px;">
                    <button type="submit" class="btn btn-success">Atualizar Postagem</button>
                    <a href="posts_questoes.php?topico_id=<?php echo $post['topico_secundario_id']; ?>" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>