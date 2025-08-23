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

// Obter total de subtópicos para calcular páginas
$sql_total = "SELECT COUNT(*) as total FROM topicos_secundarios WHERE questao_id = :questao_id";
$stmt_total = $pdo->prepare($sql_total);
$stmt_total->bindValue(':questao_id', $questao_id, PDO::PARAM_INT);
$stmt_total->execute();
$total_topicos = $stmt_total->fetch(PDO::FETCH_ASSOC)['total'];
$total_paginas = ceil($total_topicos / $itens_por_pagina);

// Obter todos os subtópicos da matéria ordenados por nome com paginação
$sql_topicos = "SELECT * FROM topicos_secundarios WHERE questao_id = :questao_id ORDER BY nome ASC LIMIT :limit OFFSET :offset";
$stmt_topicos = $pdo->prepare($sql_topicos);
$stmt_topicos->bindValue(':questao_id', $questao_id, PDO::PARAM_INT);
$stmt_topicos->bindValue(':limit', $itens_por_pagina, PDO::PARAM_INT);
$stmt_topicos->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt_topicos->execute();
$topicos = $stmt_topicos->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subtópicos - <?php echo htmlspecialchars($questao['nome']); ?> - Sistema de Estudos</title>
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

        .page-header .matery-info {
            font-size: 1.1em;
            opacity: 0.9;
            margin-top: 10px;
        }

        .topicos-container {
            display: flex;
            flex-direction: column;
            gap: 20px;
            margin-top: 30px;
        }

        .topico-card {
            background-color: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            transition: transform 0.3s, box-shadow 0.3s;
            border-left: 4px solid #3498db;
            width: 100%;
            cursor: pointer;
        }

        .topico-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .topico-card h3 {
            color: #2c3e50;
            margin-bottom: 10px;
            font-size: 1.3em;
        }

        .topico-card p {
            color: #7f8c8d;
            margin-bottom: 15px;
            font-size: 0.9em;
            line-height: 1.4;
        }

        .topico-card .btn {
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

        .topico-card .btn:hover {
            background-color: #2980b9;
        }

        .topico-card .btn-admin {
            background-color: #e74c3c;
        }

        .topico-card .btn-admin:hover {
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

        @media (max-width: 768px) {
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
                <p>Subtópicos da matéria</p>
            </div>
        </div>
    </div>
    
    <div class="container">
        <div class="main-content">
            <!-- Breadcrumb -->
            <div class="breadcrumb">
                <a href="questoes.php">Matérias</a> 
                <span> > </span>
                <strong><?php echo htmlspecialchars($questao['nome']); ?></strong>
            </div>
            
            <div class="page-header">
                <h1>📋 Subtópicos de <?php echo htmlspecialchars($questao['nome']); ?></h1>
                <p class="matery-info"><?php echo htmlspecialchars($questao['descricao']); ?></p>
            </div>
            
            <!-- Lista de Subtópicos -->
            <div class="topicos-container">
                <?php if (empty($topicos)): ?>
                    <div style="text-align: center; padding: 40px; background-color: white; border-radius: 12px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                        <h3>Nenhum subtópico cadastrado ainda</h3>
                        <p>Comece criando seu primeiro subtópico!</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($topicos as $topico): ?>
                        <div class="topico-card" onclick="window.location.href='posts_questoes.php?topico_id=<?php echo $topico['id']; ?>'">
                            <h3><?php echo htmlspecialchars($topico['nome']); ?></h3>
                            <p><?php echo htmlspecialchars(substr($topico['descricao'], 0, 100)) . '...'; ?></p>
                            <a href="posts_questoes.php?topico_id=<?php echo $topico['id']; ?>" class="btn">Ver Questões</a>
                            <?php if ($is_admin): ?>
                                <a href="editar_subtopico_materia.php?topico_id=<?php echo $topico['id']; ?>" class="btn btn-admin">Editar</a>
                                <a href="excluir_subtopico_materia.php?topico_id=<?php echo $topico['id']; ?>" class="btn btn-admin">Excluir</a>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            
            <!-- Paginação -->
            <?php if ($total_paginas > 1): ?>
                <div class="pagination">
                    <?php if ($pagina_atual > 1): ?>
                        <a href="?questao_id=<?php echo $questao_id; ?>&pagina=<?php echo $pagina_atual - 1; ?>">Anterior</a>
                    <?php else: ?>
                        <span class="disabled">Anterior</span>
                    <?php endif; ?>
                    
                    <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                        <?php if ($i == $pagina_atual): ?>
                            <span class="current"><?php echo $i; ?></span>
                        <?php else: ?>
                            <a href="?questao_id=<?php echo $questao_id; ?>&pagina=<?php echo $i; ?>"><?php echo $i; ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>
                    
                    <?php if ($pagina_atual < $total_paginas): ?>
                        <a href="?questao_id=<?php echo $questao_id; ?>&pagina=<?php echo $pagina_atual + 1; ?>">Próxima</a>
                    <?php else: ?>
                        <span class="disabled">Próxima</span>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            
            <!-- Botão de criação (somente para admin) -->
            <?php if ($is_admin): ?>
                <div style="text-align: center; margin-top: 30px;">
                    <a href="novo_subtopico_materia.php?questao_id=<?php echo $questao_id; ?>" class="btn-create">➕ Criar Novo Subtópico</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>