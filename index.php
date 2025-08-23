<?php
require_once 'includes/header.php';
require_once 'includes/auth.php';

$posts_per_page = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = $page < 1 ? 1 : $page;
$offset = ($page - 1) * $posts_per_page;

$recent_posts = getRecentPosts($pdo, $posts_per_page, $offset);
$total_posts = getTotalRecentPosts($pdo);
$total_pages = ceil($total_posts / $posts_per_page);

$topics = getTopics($pdo);

// Função para limitar caracteres da descrição
function strip_tags_and_limit($text, $limit = 150) {
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
            <h1 style="text-align: left; margin-bottom: 30px; font-family: 'Arial Black', Arial, sans-serif; font-weight: normal;">Posts Recentes 😀</h1>
            
            <div class="posts-list">
                <?php if (empty($recent_posts)): ?>
                    <p style="text-align: center; color: #f9fcfcff; font-family: Arial, sans-serif;">Nenhum post encontrado.</p>
                <?php else: ?>
                    <?php foreach ($recent_posts as $post): ?>
                        <!-- Card inteiro clicável -->
                        <div class="post-item" style="border-bottom: 0px solid #817171ff; box-shadow: 0px 0px 2px rgba(0,0,0,0.1); padding: 20px 0; position: relative; cursor: pointer;" 
                             onclick="window.location='post.php?id=<?php echo $post['id']; ?>&subtopic_id=<?php echo $post['subtopic_id']; ?>'">
                            
                            <div style="display: flex; align-items: flex-start;">
                                <div style="flex: 1; position: relative; z-index: 2;">
                                    <h2 class="post-title" style="margin-bottom: 10px; font-size: 1.3em; font-family: 'Arial Black', Arial, sans-serif;">
                                        <a href="post.php?id=<?php echo $post['id']; ?>&subtopic_id=<?php echo $post['subtopic_id']; ?>" 
                                           style="color: #000000ff; text-decoration: none; font-family: 'Arial Black', Arial, sans-serif;">
                                            <?php echo htmlspecialchars(strip_tags_and_limit($post['titulo'], 80)); ?>
                                        </a>
                                    </h2>
                                    <div class="post-date" style="color: #000000ff; font-size: 0.9em; margin-bottom: 10px; font-family: Arial, sans-serif;">
                                        <?php echo date('d/m/Y H:i', strtotime($post['data_criacao'])); ?>
                                    </div>
                                    <p class="post-description" style="color: #000000ff; margin-bottom: 15px; font-style: italic; font-family: Arial, sans-serif;">
                                        <?php echo htmlspecialchars(strip_tags_and_limit($post['descricao'], 150)); ?>
                                    </p>
                                    <p class="post-text" style="color: #000000ff; line-height: 1.6; margin-bottom: 15px; font-family: Verdana, sans-serif;">
                                        <?php echo htmlspecialchars(strip_tags_and_limit($post['texto'], 200)) . '...'; ?>
                                    </p>
                                    <small style="color: #000000ff; font-size: 0.9em; font-family: Arial, sans-serif;">
                                        <strong>Tópico:</strong> <?php echo htmlspecialchars($post['topic_nome']); ?> | 
                                        <strong>Subtópico:</strong> <?php echo htmlspecialchars($post['subtopic_nome']); ?>
                                    </small>
                                </div>
                                
                                <!-- Capa do post -->
                                <?php if (!empty($post['capa'])): ?>
                                    <div style="margin-left: 20px; flex-shrink: 0; position: relative; z-index: 2;">
                                        <img src="<?php echo htmlspecialchars($post['capa']); ?>" 
                                             alt="Capa do Post" 
                                             style="width: 150px; height: 150px; object-fit: cover; border-radius: 100px;">
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Link invisível que cobre todo o card -->
                            <a href="post.php?id=<?php echo $post['id']; ?>&subtopic_id=<?php echo $post['subtopic_id']; ?>" 
                               style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 1;"></a>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            
            <!-- Paginação -->
            <?php if ($total_pages > 1): ?>
                <div class="pagination" style="margin-top: 30px; text-align: center; font-family: Arial, sans-serif;">
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <?php if ($i == $page): ?>
                            <span class="current" style="display: inline-block; padding: 8px 16px; margin: 0 5px; background-color: #2c3e50; color: white; text-decoration: none; border-radius: 4px; font-family: Arial, sans-serif;"><?php echo $i; ?></span>
                        <?php else: ?>
                            <a href="?page=<?php echo $i; ?>" style="display: inline-block; padding: 8px 16px; margin: 0 5px; background-color: #3498db; color: white; text-decoration: none; border-radius: 4px; font-family: Arial, sans-serif;"><?php echo $i; ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Coluna Lateral - Menu de tópicos -->
        <div class="column-sidebar" style="flex: 1; background-color: white; padding: 20px; border-radius: 8px; box-shadow: 2px 5px 5px rgba(0,0,0,0.1); height: fit-content; font-family: Arial, sans-serif;">
            <h3 class="sidebar-title" style="color: #2c3e50; margin-bottom: 15px; padding-bottom: 10px; border-bottom: 2px solid #3498db; font-family: 'Arial Black', Arial, sans-serif;">Tópicos</h3>
            <ul class="sidebar-links" style="list-style: none; font-family: Arial, sans-serif;">
                <?php if (empty($topics)): ?>
                    <li style="color: #7f8c8d; font-family: Arial, sans-serif;">Nenhum tópico encontrado.</li>
                <?php else: ?>
                    <?php foreach ($topics as $topic): ?>
                        <li style="margin-bottom: 10px; font-family: Arial, sans-serif;">
                            <a href="topic.php?topico_id=<?php echo urlencode($topic['id']); ?>" 
                               style="color: #3498db; text-decoration: none; transition: color 0.3s; display: block; padding: 8px 12px; border-radius: 4px; font-family: Arial, sans-serif;">
                                <?php echo htmlspecialchars($topic['nome']); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</body>
</html>