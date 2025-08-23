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

// Configuração de paginação
$itens_por_pagina = 6;
$pagina_atual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$offset = ($pagina_atual - 1) * $itens_por_pagina;

// Obter ID do subtópico
$topico_id = isset($_GET['topico_id']) ? (int)$_GET['topico_id'] : 0;

if ($topico_id <= 0) {
    die("ID do subtópico inválido!");
}

// Verificar se o subtópico existe
$sql_check_topico = "SELECT id, questao_id, nome, descricao FROM topicos_secundarios WHERE id = :id";
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

// Obter total de posts para calcular páginas
$sql_total = "SELECT COUNT(*) as total FROM posts_questoes WHERE topico_secundario_id = :topico_id";
$stmt_total = $pdo->prepare($sql_total);
$stmt_total->bindValue(':topico_id', $topico_id, PDO::PARAM_INT);
$stmt_total->execute();
$total_posts = $stmt_total->fetch(PDO::FETCH_ASSOC)['total'];
$total_paginas = ceil($total_posts / $itens_por_pagina);

// Obter todos os posts do subtópico ordenados por título com paginação
$sql_posts = "SELECT * FROM posts_questoes WHERE topico_secundario_id = :topico_id ORDER BY titulo ASC LIMIT :limit OFFSET :offset";
$stmt_posts = $pdo->prepare($sql_posts);
$stmt_posts->bindValue(':topico_id', $topico_id, PDO::PARAM_INT);
$stmt_posts->bindValue(':limit', $itens_por_pagina, PDO::PARAM_INT);
$stmt_posts->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt_posts->execute();
$posts = $stmt_posts->fetchAll(PDO::FETCH_ASSOC);

// Obter estatísticas específicas para este subtópico
$total_acertos = 0;
$total_erros = 0;
$total_marcadas = 0;

try {
    // Obter total de posts marcados neste subtópico
    $sql_total = "SELECT COUNT(*) as total FROM posts_questoes WHERE topico_secundario_id = :topico_id AND status IS NOT NULL";
    $stmt_total = $pdo->prepare($sql_total);
    $stmt_total->bindValue(':topico_id', $topico_id, PDO::PARAM_INT);
    $stmt_total->execute();
    $total_marcadas = $stmt_total->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Obter total de acertos neste subtópico
    $sql_acertos = "SELECT COUNT(*) as total FROM posts_questoes WHERE topico_secundario_id = :topico_id AND status = 'acertou'";
    $stmt_acertos = $pdo->prepare($sql_acertos);
    $stmt_acertos->bindValue(':topico_id', $topico_id, PDO::PARAM_INT);
    $stmt_acertos->execute();
    $total_acertos = $stmt_acertos->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Obter total de erros neste subtópico
    $sql_erros = "SELECT COUNT(*) as total FROM posts_questoes WHERE topico_secundario_id = :topico_id AND status = 'errou'";
    $stmt_erros = $pdo->prepare($sql_erros);
    $stmt_erros->bindValue(':topico_id', $topico_id, PDO::PARAM_INT);
    $stmt_erros->execute();
    $total_erros = $stmt_erros->fetch(PDO::FETCH_ASSOC)['total'];
    
} catch (PDOException $e) {
    error_log("Erro ao buscar estatísticas: " . $e->getMessage());
    $total_acertos = 0;
    $total_erros = 0;
    $total_marcadas = 0;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Posts - <?php echo htmlspecialchars($topico['nome']); ?> - Sistema de Estudos</title>
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
            max-width: 1200px;
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
        }

        .page-header {
            text-align: center;
            margin-bottom: 30px;
            padding: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .page-header h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
        }

        .page-header .topico-info {
            font-size: 1.1em;
            opacity: 0.9;
            margin-top: 10px;
        }

        .stats-container {
            display: flex;
            justify-content: center;
            gap: 30px;
            margin: 20px 0;
            flex-wrap: wrap;
        }

        .stat-card {
            background-color: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            text-align: center;
            min-width: 150px;
        }

        .stat-value {
            font-size: 2em;
            font-weight: bold;
        }

        .stat-label {
            font-size: 1em;
            color: #666;
        }

        .stat-acertos {
            color: #27ae60;
        }

        .stat-erros {
            color: #e74c3c;
        }

        .posts-container {
            display: flex;
            flex-direction: column;
            gap: 20px;
            margin-top: 30px;
        }

        .post-card {
            background-color: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            transition: transform 0.3s, box-shadow 0.3s;
            border-left: 4px solid #3498db;
            width: 100%;
            cursor: pointer;
        }

        .post-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .post-card h3 {
            color: #2c3e50;
            margin-bottom: 10px;
            font-size: 1.3em;
        }

        .post-card p {
            color: #7f8c8d;
            margin-bottom: 15px;
            font-size: 0.9em;
            line-height: 1.4;
        }

        .post-card .nivel-dificuldade {
            display: inline-block;
            padding: 4px 8px;
            background-color: #f1c40f;
            color: white;
            border-radius: 4px;
            font-size: 0.8em;
            margin-bottom: 10px;
        }

        .post-card .status {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.8em;
            margin-bottom: 10px;
        }

        .status-acertou {
            background-color: #27ae60;
            color: white;
        }

        .status-errou {
            background-color: #e74c3c;
            color: white;
        }

        .post-card .btn {
            display: inline-block;
            padding: 8px 15px;
            background-color: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-size: 0.9em;
            margin-right: 5px;
            transition: background-color 0.3s;
            margin-bottom: 5px;
        }

        .post-card .btn:hover {
            background-color: #2980b9;
        }

        .post-card .btn-admin {
            background-color: #e74c3c;
        }

        .post-card .btn-admin:hover {
            background-color: #c0392b;
        }

        .btn-create {
            display: block;
            width: 100%;
            padding: 12px;
            background-color: #27ae60;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            text-align: center;
            font-weight: bold;
            margin-top: 15px;
            transition: background-color 0.3s;
        }

        .btn-create:hover {
            background-color: #219653;
        }

        .breadcrumb {
            margin-bottom: 20px;
            padding: 10px;
            background-color: #ecf0f1;
            border-radius: 8px;
        }

        .breadcrumb a {
            color: #3498db;
            text-decoration: none;
        }

        .breadcrumb a:hover {
            text-decoration: underline;
        }

        .breadcrumb span {
            color: #7f8c8d;
        }

        /* Paginação */
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            margin: 30px 0;
            flex-wrap: wrap;
        }

        .pagination a, .pagination span {
            padding: 10px 15px;
            border: 1px solid #ddd;
            text-decoration: none;
            color: #333;
            border-radius: 4px;
            transition: all 0.3s;
        }

        .pagination a:hover {
            background-color: #3498db;
            color: white;
            border-color: #3498db;
        }

        .pagination .current {
            background-color: #3498db;
            color: white;
            border-color: #3498db;
        }

        .pagination .disabled {
            color: #ccc;
            cursor: default;
            border-color: #eee;
        }

        /* Estilo para conteúdo HTML truncado */
        .post-content-preview {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        @media (max-width: 768px) {
            .stats-container {
                flex-direction: column;
                align-items: center;
            }
            
            .page-header h1 {
                font-size: 2em;
            }
        }
    </style>
</head>
<body>
    <!-- Header com imagem -->
    <div class="header">
        <div style="text-align: center; padding: 10px 0;">
            <img src="uploads/questoes.webp" alt="Logo do Grupo Nofap" class="header-image" onerror="this.style.display='none';">
            <div class="header-content">
                <a href="index.php" style="display: inline-block; padding: 10px 20px; background: linear-gradient(135deg, #ff6b6b, #ee5a24); color: white; text-decoration: none; border-radius: 25px; font-weight: bold; font-size: 1em; transition: all 0.3s ease; box-shadow: 0 4px 6px rgba(0,0,0,0.1); border: none; cursor: pointer; margin: 5px;">🏠 Página Inicial</a>
                <h1>Sistema de Estudos</h1>
                <p>Questões do subtópico</p>
            </div>
        </div>
    </div>
    
    <div class="container">
        <div class="main-content">
            <!-- Breadcrumb -->
            <div class="breadcrumb">
                <a href="questoes.php">Matérias</a> 
                <span> > </span>
                <a href="topicos_questoes.php?questao_id=<?php echo $topico['questao_id']; ?>">
                    <?php echo htmlspecialchars($questao['questao_nome']); ?>
                </a>
                <span> > </span>
                <strong><?php echo htmlspecialchars($topico['nome']); ?></strong>
            </div>
            
            <div class="page-header">
                <h1>📝 Questões</h1>
                <p class="topico-info"><?php echo htmlspecialchars($topico['descricao']); ?></p>
            </div>
            
            <!-- Estatísticas Específicas para Este Subtópico -->
            <div class="stats-container">
                <div class="stat-card">
                    <div class="stat-value stat-acertos"><?php echo $total_acertos; ?></div>
                    <div class="stat-label">Acertos</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value stat-erros"><?php echo $total_erros; ?></div>
                    <div class="stat-label">Erros</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value"><?php echo $total_marcadas; ?></div>
                    <div class="stat-label">Total Feitas</div>
                </div>
            </div>
            
            <!-- Lista de Posts -->
            <div class="posts-container">
                <?php if (empty($posts)): ?>
                    <div style="text-align: center; padding: 40px; background-color: white; border-radius: 12px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                        <h3>Nenhum post cadastrado ainda</h3>
                        <p>Comece criando sua primeira questão!</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($posts as $post): ?>
                        <div class="post-card" onclick="window.location.href='visualizacao_completa_questoes.php?post_id=<?php echo $post['id']; ?>'">
                            <h3><?php echo htmlspecialchars($post['titulo']); ?></h3>
                            <div class="post-content-preview">
                                <?php 
                                // Limpar o conteúdo para exibir apenas texto sem tags HTML para o preview
                                $conteudo_sem_html = strip_tags($post['conteudo']);
                                echo htmlspecialchars(substr($conteudo_sem_html, 0, 200)) . '...';
                                ?>
                            </div>
                            <span class="nivel-dificuldade"><?php echo "Nível:\t".htmlspecialchars($post['nivel_dificuldade']); ?></span>
                            <?php if ($post['status']): ?>
                                <span class="status status-<?php echo $post['status']; ?>">
                                    <?php echo $post['status'] === 'acertou' ? '✅ Acertou' : '❌ Errou'; ?>
                                </span>
                            <?php endif; ?>
                            <p><small>Data de criação: <?php echo date('d/m/Y', strtotime($post['created_at'])); ?></small></p>
                            <a href="visualizacao_completa_questoes.php?post_id=<?php echo $post['id']; ?>" class="btn">Ver Questão Completa</a>
                            <?php if ($is_admin): ?>
                                <a href="editar_postagem_questoes.php?post_id=<?php echo $post['id']; ?>" class="btn btn-admin">Editar</a>
                                <a href="excluir_postagem_questoes.php?post_id=<?php echo $post['id']; ?>" class="btn btn-admin" onclick="return confirm('Tem certeza que deseja excluir esta postagem?')">Excluir</a>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            
            <!-- Paginação -->
            <?php if ($total_paginas > 1): ?>
                <div class="pagination">
                    <?php if ($pagina_atual > 1): ?>
                        <a href="?topico_id=<?php echo $topico_id; ?>&pagina=<?php echo $pagina_atual - 1; ?>">Anterior</a>
                    <?php else: ?>
                        <span class="disabled">Anterior</span>
                    <?php endif; ?>
                    
                    <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                        <?php if ($i == $pagina_atual): ?>
                            <span class="current"><?php echo $i; ?></span>
                        <?php else: ?>
                            <a href="?topico_id=<?php echo $topico_id; ?>&pagina=<?php echo $i; ?>"><?php echo $i; ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>
                    
                    <?php if ($pagina_atual < $total_paginas): ?>
                        <a href="?topico_id=<?php echo $topico_id; ?>&pagina=<?php echo $pagina_atual + 1; ?>">Próxima</a>
                    <?php else: ?>
                        <span class="disabled">Próxima</span>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            
            <!-- Botão de criação (somente para admin) -->
            <?php if ($is_admin): ?>
                <div style="text-align: center; margin-top: 30px;">
                    <a href="criar_nova_postagem.php?topico_id=<?php echo $topico_id; ?>" class="btn-create">➕ Criar Nova Postagem</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>