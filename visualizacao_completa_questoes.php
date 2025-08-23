<?php
// Iniciar sessão
session_start();

// Incluindo configurações do banco de dados
require_once 'includes/config.php';

// Verificar se é administrador
$is_admin = false;
if (isset($_SESSION['user_id']) && $_SESSION['user_id'] !== null) {
    $is_admin = true;
}

// Obter ID da postagem
$post_id = isset($_GET['post_id']) ? (int)$_GET['post_id'] : 0;

if ($post_id <= 0) {
    die("ID da postagem inválido!");
}

// Verificar se a postagem existe
$sql_check_post = "SELECT p.*, t.nome as topico_nome, t.questao_id, q.nome as questao_nome 
                   FROM posts_questoes p
                   JOIN topicos_secundarios t ON p.topico_secundario_id = t.id
                   JOIN questoes q ON t.questao_id = q.id
                   WHERE p.id = :id";
$stmt_check = $pdo->prepare($sql_check_post);
$stmt_check->bindValue(':id', $post_id, PDO::PARAM_INT);
$stmt_check->execute();

$post = $stmt_check->fetch(PDO::FETCH_ASSOC);

if (!$post) {
    die("Postagem não encontrada!");
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visualização Completa - Sistema de Estudos</title>
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

        .post-details {
            margin-bottom: 30px;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 8px;
            border-left: 4px solid #3498db;
        }

        .post-details h2 {
            color: #2c3e50;
            margin-bottom: 15px;
        }

        .post-meta {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 10px;
        }

        .meta-item {
            background-color: #e3f2fd;
            padding: 8px 15px;
            border-radius: 4px;
            font-size: 0.9em;
        }

        .nivel-dificuldade {
            display: inline-block;
            padding: 6px 12px;
            background-color: #f1c40f;
            color: white;
            border-radius: 4px;
            font-size: 0.9em;
            margin: 5px 0;
        }

        .status {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 0.9em;
            margin: 5px 0;
        }

        .status-acertou {
            background-color: #27ae60;
            color: white;
        }

        .status-errou {
            background-color: #e74c3c;
            color: white;
        }

        .post-content {
            margin: 20px 0;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            border: 1px solid #eee;
        }

        .post-content h3 {
            color: #2c3e50;
            margin-bottom: 15px;
        }

        .post-content p {
            margin-bottom: 15px;
            line-height: 1.6;
        }

        /* Estilo para imagens */
        .post-content img {
            max-width: 500px;
            height: auto;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            margin: 10px 0;
            display: block;
            margin-left: auto;
            margin-right: auto;
        }

        /* Estilo para conteúdo HTML */
        .post-content .html-content {
            white-space: pre-line;
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

        .btn-admin {
            background-color: #e74c3c;
        }

        .btn-admin:hover {
            background-color: #c0392b;
        }

        .btn-secondary {
            background-color: #95a5a6;
        }

        .btn-secondary:hover {
            background-color: #7f8c8d;
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
            
            .post-meta {
                flex-direction: column;
            }
            
            .post-content img {
                max-width: 100%;
                height: auto;
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
                <p>Visualização Completa da Questão</p>
            </div>
        </div>
    </div>
    
    <div class="container">
        <div class="main-content">
            <a href="posts_questoes.php?topico_id=<?php echo $post['topico_secundario_id']; ?>" class="back-link">← Voltar para Posts</a>
            
            <div class="page-header">
                <h1>📖 Questão Completa</h1>
            </div>
            
            <div class="post-details">
                <h2><?php echo htmlspecialchars($post['titulo']); ?></h2>
                
                <div class="post-meta">
                    <div class="meta-item">Matéria: <?php echo htmlspecialchars($post['questao_nome']); ?></div>
                    <div class="meta-item">Subtópico: <?php echo htmlspecialchars($post['topico_nome']); ?></div>
                    <div class="meta-item">Data de Criação: <?php echo date('d/m/Y H:i', strtotime($post['created_at'])); ?></div>
                </div>
                
                <div class="meta-item">
                    <span class="nivel-dificuldade"><?php echo "nível:\t" . htmlspecialchars($post['nivel_dificuldade']); ?></span>
                    <?php if ($post['status']): ?>
                        <span class="status status-<?php echo $post['status']; ?>">
                            <?php echo $post['status'] === 'acertou' ? '✅ Acertou' : '❌ Errou'; ?>
                        </span>
                    <?php else: ?>
                        <span class="status">⚠️ Sem Status</span>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="post-content">
                <h3>Conteúdo da Questão:</h3>
                <div class="html-content">
                    <?php 
                    // Processar o conteúdo HTML para exibir imagens com tamanho fixo
                    $conteudo = $post['conteudo'];
                    // Substituir tags img para ter tamanho fixo
                    $conteudo = preg_replace('/<img([^>]*)src=["\']([^"\']+)["\']([^>]*)>/i', '<img$1src="$2"$3 style="max-width: 500px; height: auto; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); margin: 10px 0; display: block; margin-left: auto; margin-right: auto;">', $conteudo);
                    echo $conteudo;
                    ?>
                </div>
            </div>
            
            <?php if ($is_admin): ?>
                <div style="text-align: center; margin-top: 30px;">
                    <a href="editar_postagem_questoes.php?post_id=<?php echo $post['id']; ?>" class="btn btn-admin">✏️ Editar Questão</a>
                    <a href="excluir_postagem_questoes.php?post_id=<?php echo $post['id']; ?>" class="btn btn-admin">🗑️ Excluir Questão</a>
                    <a href="posts_questoes.php?topico_id=<?php echo $post['topico_secundario_id']; ?>" class="btn btn-secondary">↩️ Voltar para Posts</a>
                </div>
            <?php else: ?>
                <div style="text-align: center; margin-top: 30px;">
                    <a href="posts_questoes.php?topico_id=<?php echo $post['topico_secundario_id']; ?>" class="btn btn-secondary">↩️ Voltar para Posts</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>