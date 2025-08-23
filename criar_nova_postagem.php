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

// Verificar se o subtópico existe
if ($topico_id > 0) {
    $sql_check_topico = "SELECT id FROM topicos_secundarios WHERE id = :id";
    $stmt_check = $pdo->prepare($sql_check_topico);
    $stmt_check->bindValue(':id', $topico_id, PDO::PARAM_INT);
    $stmt_check->execute();
    
    if ($stmt_check->rowCount() == 0) {
        die("Subtópico não encontrado!");
    }
} else {
    die("ID do subtópico inválido!");
}

// Processar o cadastro da nova postagem
$mensagem = '';
$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo'] ?? '');
    $conteudo = trim($_POST['conteudo'] ?? '');
    $nivel_dificuldade = trim($_POST['nivel_dificuldade'] ?? 'médio');
    $status = trim($_POST['status'] ?? null);
    
    if (!empty($titulo) && !empty($conteudo)) {
        // Inserir nova postagem
        $sql_insert = "INSERT INTO posts_questoes (topico_secundario_id, titulo, conteudo, nivel_dificuldade, status) VALUES (:topico_id, :titulo, :conteudo, :nivel_dificuldade, :status)";
        $stmt_insert = $pdo->prepare($sql_insert);
        $stmt_insert->bindValue(':topico_id', $topico_id, PDO::PARAM_INT);
        $stmt_insert->bindValue(':titulo', $titulo, PDO::PARAM_STR);
        $stmt_insert->bindValue(':conteudo', $conteudo, PDO::PARAM_STR);
        $stmt_insert->bindValue(':nivel_dificuldade', $nivel_dificuldade, PDO::PARAM_STR);
        $stmt_insert->bindValue(':status', $status, PDO::PARAM_STR);
        
        if ($stmt_insert->execute()) {
            $mensagem = "Postagem '$titulo' criada com sucesso!";
            // Redirecionar para a página de posts
            header("Location: posts_questoes.php?topico_id=$topico_id");
            exit();
        } else {
            $erro = "Erro ao criar a postagem!";
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
    <title>Criar Nova Postagem - Sistema de Estudos</title>
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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

        /* Estilo para preview de imagens */
        .preview-container {
            margin-top: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 8px;
            border: 1px solid #e9ecef;
        }

        .preview-title {
            font-weight: bold;
            margin-bottom: 10px;
            color: #2c3e50;
        }

        .preview-content {
            margin-top: 10px;
        }

        .preview-content img {
            max-width: 100%;
            height: auto;
            border-radius: 4px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
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
                <p>Criar Nova Postagem</p>
            </div>
        </div>
    </div>
    
    <div class="container">
        <div class="main-content">
            <a href="posts_questoes.php?topico_id=<?php echo $topico_id; ?>" class="back-link">← Voltar para Posts</a>
            
            <div class="page-header">
                <h1>✏️ Criar Nova Postagem</h1>
                <p>Adicione uma nova questão ao subtópico</p>
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
                    <input type="text" id="titulo" name="titulo" placeholder="Ex: Questão sobre cinemática..." required>
                </div>
                
                <div class="form-group">
                    <label for="nivel_dificuldade">Nível de Dificuldade:</label>
                    <select id="nivel_dificuldade" name="nivel_dificuldade">
                        <option value="fácil">Fácil</option>
                        <option value="médio" selected>Médio</option>
                        <option value="difícil">Difícil</option>
                        <option value="muito difícil">Muito Difícil</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="status">Status (opcional):</label>
                    <select id="status" name="status">
                        <option value="">Sem status</option>
                        <option value="acertou">Acertou</option>
                        <option value="errou">Errou</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="conteudo">Conteúdo da Questão:</label>
                    <textarea id="conteudo" name="conteudo" placeholder="Digite o conteúdo completo da questão...&#10;&#10;Você pode incluir imagens usando:&#10;<img src='questoes/nome-da-imagem.ext'>&#10;&#10;Exemplo:&#10;<img src='questoes/exemplo.png'>" required></textarea>
 
                </div>
                
                <div style="text-align: center; margin-top: 30px;">
                    <button type="submit" class="btn btn-success">Criar Postagem</button>
                    <a href="posts_questoes.php?topico_id=<?php echo $topico_id; ?>" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>