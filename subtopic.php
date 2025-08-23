<?php
// Incluindo o header e configurações
require_once 'includes/header.php';
require_once 'includes/auth.php'; // Adicionar autenticação

// Verificar se o subtópico foi fornecido
if (!isset($_GET['subtopic_id']) || empty($_GET['subtopic_id'])) {
    die("Subtópico inválido");
}

$subtopic_id = (int)$_GET['subtopic_id'];

// Configurações de paginação
$posts_per_page = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = $page < 1 ? 1 : $page; // Garantir que a página não seja menor que 1
$offset = ($page - 1) * $posts_per_page;

// Obter informações do subtópico diretamente do banco
$sql = "SELECT s.*, t.nome as topic_nome FROM subtopics s 
        JOIN topics t ON s.topic_id = t.id 
        WHERE s.id = :id";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':id', $subtopic_id, PDO::PARAM_INT);
$stmt->execute();
$subtopic_info = $stmt->fetch(PDO::FETCH_ASSOC);

// Inicializar variáveis de mensagem e erro
$mensagem = '';
$erro = '';

// Processar requisições POST para posts (APENAS PARA ADMINISTRADORES)
if (isAdmin() && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_post'])) {
    switch ($_POST['action_post']) {
        case 'delete':
            if (!empty($_POST['id'])) {
                $id = (int)$_POST['id'];
                if (deletePost($pdo, $id)) {
                    $mensagem = "Post excluído com sucesso!";
                } else {
                    $erro = "Erro ao excluir o post!";
                }
            }
            break;
    }
}

// Obter posts do subtópico
$posts = getPostsBySubtopic($pdo, $subtopic_id, $posts_per_page, $offset);
$total_posts = getTotalPostsBySubtopic($pdo, $subtopic_id);
$total_pages = ceil($total_posts / $posts_per_page);

// Função para remover tags HTML e limitar texto
function strip_tags_and_limit($text, $limit = 200) {
    // Remove tags HTML
    $text_without_tags = strip_tags($text);
    // Limita o tamanho
    if (strlen($text_without_tags) > $limit) {
        return substr($text_without_tags, 0, $limit) . '...';
    }
    return $text_without_tags;
}
?>

        <!-- Coluna Principal -->
        <div class="column-main">
            <!-- Título centralizado - APENAS "Posts deste Subtópico" -->
            <h1 style="text-align: center; margin-bottom: 25px; font-family: 'Crimson Text', Georgia, serif; font-weight: normal; color: #000000ff; padding-bottom: 10px; border-bottom: 1px solid #a6d7f7ff;">Posts deste Subtópico</h1>
            
            <?php if ($mensagem): ?>
                <div style="background-color: #d4edda; color: #155724; padding: 10px; margin-bottom: 15px; border-radius: 4px;">
                    <?php echo htmlspecialchars($mensagem); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($erro): ?>
                <div style="background-color: #f8d7da; color: #721c24; padding: 10px; margin-bottom: 15px; border-radius: 4px;">
                    <?php echo htmlspecialchars($erro); ?>
                </div>
            <?php endif; ?>
            
            <!-- Link para administração de posts - APENAS PARA ADMINISTRADORES -->
            <?php if (isAdmin()): ?>
                <div style="margin-bottom: 15px; text-align: center;">
                    <a href="post_admin.php?subtopic_id=<?php echo $subtopic_id; ?>" style="background-color: #6c757d; color: white; padding: 8px 15px; text-decoration: none; border-radius: 4px;">Criar Novo Post</a>
                </div>
            <?php endif; ?>
            
            <div class="posts-list">
                <?php if (empty($posts)): ?>
                    <p style="text-align: center; color: #7f8c8d;">Nenhum post encontrado neste subtópico.</p>
                <?php else: ?>
                    <?php foreach ($posts as $post): ?>
                        <!-- CARD COM TAMANHO FIXO E POSIÇÃO FIXA DOS BOTÕES -->
                        <div class="post-item" style="background-color: white; border-bottom: 1px solid #eee; padding: 20px; margin-bottom: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); height: 300px; overflow: hidden; display: flex; flex-direction: column; position: relative;">
                            
                            <!-- Conteúdo clicável -->
                            <div style="flex: 1; cursor: pointer;" onclick="window.location='post.php?id=<?php echo $post['id']; ?>&subtopic_id=<?php echo $subtopic_id; ?>'">
                                <h2 class="post-title" style="text-align: center; margin-bottom: 10px; font-size: 1.3em; flex-shrink: 0;">
                                    <a href="post.php?id=<?php echo $post['id']; ?>&subtopic_id=<?php echo $subtopic_id; ?>" style="color: #2c3e50; text-decoration: none; font-family: 'Crimson Text', Georgia, serif; font-weight: normal;">
                                        <?php echo htmlspecialchars(strip_tags_and_limit($post['titulo'], 50)); ?>
                                    </a>
                                </h2>
                                
                                <!-- Mostrar capa se existir - TAMANHO MINIATURA FIXO -->
                                <?php if (!empty($post['capa'])): ?>
                                    <div style="margin-bottom: 15px; text-align: center; flex-shrink: 0;">
                                        <img src="<?php echo htmlspecialchars($post['capa']); ?>" alt="Capa do Post" style="max-width: 150px; max-height: 100px; object-fit: cover; border-radius: 8px; display: inline-block;">
                                    </div>
                                <?php endif; ?>
                                
                                <div class="post-date" style="color: #7f8c8d; font-size: 0.9em; margin-bottom: 10px; flex-shrink: 0; font-family: 'Crimson Text', Georgia, serif; text-align: center;">
                                    <?php echo date('d/m/Y H:i', strtotime($post['data_criacao'])); ?>
                                </div>
                                <p class="post-description" style="color: #555; margin-bottom: 15px; flex-shrink: 0; font-family: 'Crimson Text', Georgia, serif; font-style: italic; text-align: center;">
                                    <?php echo htmlspecialchars(strip_tags_and_limit($post['descricao'], 80)); ?>
                                </p>
                                <p class="post-text" style="color: #666; line-height: 1.6; margin-bottom: 20px; flex-grow: 1; overflow: hidden; font-family: 'Crimson Text', Georgia, serif; text-align: center;">
                                    <?php echo htmlspecialchars(strip_tags_and_limit($post['texto'], 100)) . '...'; ?>
                                </p>
                            </div>
                            
                            <!-- Botões individuais - APENAS PARA ADMINISTRADORES - POSIÇÃO FIXA -->
                            <?php if (isAdmin()): ?>
                                <div style="position: absolute; bottom: 10px; right: 10px; z-index: 2;">
                                    <a href="edit_post.php?id=<?php echo $post['id']; ?>&subtopic_id=<?php echo $subtopic_id; ?>" style="background-color: #ffc107; color: #212529; padding: 6px 12px; text-decoration: none; border-radius: 4px; margin-right: 5px; font-size: 0.85em; font-family: 'Crimson Text', Georgia, serif;">Editar</a>
                                    <form method="post" action="" style="display: inline;" onsubmit="return confirm('Tem certeza que deseja excluir este post?');">
                                        <input type="hidden" name="action_post" value="delete">
                                        <input type="hidden" name="id" value="<?php echo $post['id']; ?>">
                                        <button type="submit" style="background-color: #dc3545; color: white; padding: 6px 12px; border: none; border-radius: 4px; cursor: pointer; font-size: 0.85em; font-family: 'Crimson Text', Georgia, serif;">Excluir</button>
                                    </form>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            
            <!-- Paginação -->
            <?php if ($total_pages > 1): ?>
                <div class="pagination" style="margin-top: 30px; text-align: center;">
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <?php if ($i == $page): ?>
                            <span class="current" style="display: inline-block; padding: 8px 16px; margin: 0 5px; background-color: #2c3e50; color: white; text-decoration: none; border-radius: 4px;"><?php echo $i; ?></span>
                        <?php else: ?>
                            <a href="?subtopic_id=<?php echo $subtopic_id; ?>&page=<?php echo $i; ?>" style="display: inline-block; padding: 8px 16px; margin: 0 5px; background-color: #3498db; color: white; text-decoration: none; border-radius: 4px;"><?php echo $i; ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Coluna Lateral - Removida -->
    </div>
</body>
</html>