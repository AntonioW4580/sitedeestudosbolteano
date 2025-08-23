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

// Obter todas as questões ordenadas por nome com paginação
$sql_questoes = "SELECT * FROM questoes ORDER BY nome ASC LIMIT :limit OFFSET :offset";
$stmt_questoes = $pdo->prepare($sql_questoes);
$stmt_questoes->bindValue(':limit', $itens_por_pagina, PDO::PARAM_INT);
$stmt_questoes->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt_questoes->execute();
$questoes = $stmt_questoes->fetchAll(PDO::FETCH_ASSOC);

// Obter total de questões para calcular páginas
$sql_total = "SELECT COUNT(*) as total FROM questoes";
$stmt_total = $pdo->prepare($sql_total);
$stmt_total->execute();
$total_questoes = $stmt_total->fetch(PDO::FETCH_ASSOC)['total'];
$total_paginas = ceil($total_questoes / $itens_por_pagina);

// Obter estatísticas globais (todas as matérias)
$estatisticas = ['total_acertos' => 0, 'total_erros' => 0, 'total_posts' => 0];

try {
    // Obter total de posts de todas as matérias
    $sql_total = "SELECT COUNT(*) as total FROM posts_questoes";
    $stmt_total = $pdo->prepare($sql_total);
    $stmt_total->execute();
    $estatisticas['total_posts'] = $stmt_total->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Obter total de acertos de todas as matérias
    $sql_acertos = "SELECT COUNT(*) as total FROM posts_questoes WHERE status = 'acertou'";
    $stmt_acertos = $pdo->prepare($sql_acertos);
    $stmt_acertos->execute();
    $estatisticas['total_acertos'] = $stmt_acertos->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Obter total de erros de todas as matérias
    $sql_erros = "SELECT COUNT(*) as total FROM posts_questoes WHERE status = 'errou'";
    $stmt_erros = $pdo->prepare($sql_erros);
    $stmt_erros->execute();
    $estatisticas['total_erros'] = $stmt_erros->fetch(PDO::FETCH_ASSOC)['total'];
    
} catch (PDOException $e) {
    error_log("Erro ao buscar estatísticas: " . $e->getMessage());
    $estatisticas = ['total_acertos' => 0, 'total_erros' => 0, 'total_posts' => 0];
}
?>


<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Questões - Sistema de Estudos</title>
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

        .quests-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }

        .quest-card {
            background-color: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            transition: transform 0.3s, box-shadow 0.3s;
            border-left: 4px solid #3498db;
        }

        .quest-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .quest-card h3 {
            color: #2c3e50;
            margin-bottom: 10px;
            font-size: 1.3em;
        }

        .quest-card p {
            color: #7f8c8d;
            margin-bottom: 15px;
            font-size: 0.9em;
            line-height: 1.4;
        }

        .quest-card .btn {
            display: inline-block;
            padding: 8px 15px;
            background-color: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-size: 0.9em;
            margin-right: 5px;
            transition: background-color 0.3s;
        }

        .quest-card .btn:hover {
            background-color: #2980b9;
        }

        .quest-card .btn-admin {
            background-color: #e74c3c;
        }

        .quest-card .btn-admin:hover {
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

        @media (max-width: 768px) {
            .quests-container {
                grid-template-columns: 1fr;
            }
            
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
            <img src="uploads/questoes.webp" alt="Estudos" class="header-image" onerror="this.style.display='none';">
            <div class="header-content">
            <a href="index.php" style="display: inline-block; padding: 10px 20px; background: linear-gradient(135deg, #ff6b6b, #ee5a24); color: white; text-decoration: none; border-radius: 25px; font-weight: bold; font-size: 1em; transition: all 0.3s ease; box-shadow: 0 4px 6px rgba(0,0,0,0.1); border: none; cursor: pointer; margin: 5px;">🏠 Página Inicial</a>
                <h1>Sistema de Estudos</h1>
                <p>Todas as questões que eu irei estudar estarão aqui.</p>
            </div>
        </div>
    </div>
    
    <div class="container">
        <div class="main-content">
            <div class="page-header">
                <h1>📚 Questões Para Estudar</h1>
                <p>Organize e acompanhe suas questões por matérias e assuntos</p>
            </div>
            
            <!-- Estatísticas Globais -->
            <div class="stats-container">
                <div class="stat-card">
                    <div class="stat-value stat-acertos"><?php echo $estatisticas['total_acertos']; ?></div>
                    <div class="stat-label">Acertos Totais</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value stat-erros"><?php echo $estatisticas['total_erros']; ?></div>
                    <div class="stat-label">Erros Totais</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value"><?php echo $estatisticas['total_posts']; ?></div>
                    <div class="stat-label">Questões Totais</div>
                </div>
            </div>
            
            <!-- Lista de Questões -->
            <div class="quests-container">
                <?php if (empty($questoes)): ?>
                    <div style="grid-column: 1 / -1; text-align: center; padding: 40px; background-color: white; border-radius: 12px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                        <h3>Nenhuma matéria cadastrada ainda</h3>
                        <p>Comece criando sua primeira matéria!</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($questoes as $questao): ?>
                        <div class="quest-card">
                            <h3><?php echo htmlspecialchars($questao['nome']); ?></h3>
                            <p><?php echo htmlspecialchars(substr($questao['descricao'], 0, 100)) . '...'; ?></p>
                            <a href="topicos_questoes.php?questao_id=<?php echo $questao['id']; ?>" class="btn">Ver Subtópicos</a>
                            <?php if ($is_admin): ?>
                                <a href="editar_materia.php?questao_id=<?php echo $questao['id']; ?>" class="btn btn-admin">Editar</a>
                                <a href="excluir_materia.php?questao_id=<?php echo $questao['id']; ?>" class="btn btn-admin" onclick="return confirm('Tem certeza que deseja excluir esta matéria?')">Excluir</a>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            
            <!-- Paginação -->
            <?php if ($total_paginas > 1): ?>
                <div class="pagination">
                    <?php if ($pagina_atual > 1): ?>
                        <a href="?pagina=<?php echo $pagina_atual - 1; ?>">Anterior</a>
                    <?php else: ?>
                        <span class="disabled">Anterior</span>
                    <?php endif; ?>
                    
                    <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                        <?php if ($i == $pagina_atual): ?>
                            <span class="current"><?php echo $i; ?></span>
                        <?php else: ?>
                            <a href="?pagina=<?php echo $i; ?>"><?php echo $i; ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>
                    
                    <?php if ($pagina_atual < $total_paginas): ?>
                        <a href="?pagina=<?php echo $pagina_atual + 1; ?>">Próxima</a>
                    <?php else: ?>
                        <span class="disabled">Próxima</span>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            
            <!-- Botão de criação (somente para admin) -->
            <?php if ($is_admin): ?>
                <div style="text-align: center; margin-top: 30px;">
                    <a href="criar_nova_materia.php" class="btn-create">➕ Criar Nova Matéria</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>