<?php
// Incluindo o header e configurações
require_once 'includes/header.php';
require_once 'includes/auth.php';

// Verificar se o tópico foi fornecido
if (!isset($_GET['topico_id']) || empty($_GET['topico_id'])) {
    die("Tópico inválido");
}

$topico_id = (int)$_GET['topico_id'];

// Configurações de paginação para subtópicos
$subtopics_per_page = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = $page < 1 ? 1 : $page;
$offset = ($page - 1) * $subtopics_per_page;

// Obter informações do tópico
function getTopicInfo($pdo, $topico_id) {
    $sql = "SELECT * FROM topics WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':id', $topico_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Processar requisições POST para subtópicos (APENAS PARA ADMINS)
$mensagem_subtopic = '';
$erro_subtopic = '';

// Verificar se é uma requisição POST para subtópicos E usuário é admin
if (isAdmin() && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_subtopic'])) {
    switch ($_POST['action_subtopic']) {
        case 'create':
            if (!empty($_POST['subtopico'])) {
                $subtopico = trim($_POST['subtopico']);
                $descricao = trim($_POST['descricao']);
                $capa = '';
                
                // Tratar upload de capa se houver
                if (isset($_FILES['capa']) && $_FILES['capa']['error'] === UPLOAD_ERR_OK) {
                    $upload_dir = 'uploads/';
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0777, true);
                    }
                    
                    $file = $_FILES['capa'];
                    $allowed_types = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
                    $file_type = mime_content_type($file['tmp_name']);
                    
                    if (in_array($file_type, $allowed_types)) {
                        $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                        $new_filename = uniqid() . '.' . $file_extension;
                        $upload_path = $upload_dir . $new_filename;
                        
                        if (move_uploaded_file($file['tmp_name'], $upload_path)) {
                            $capa = $upload_path;
                        }
                    }
                }
                
                $sql = "INSERT INTO subtopics (topic_id, nome, descricao, capa) VALUES (:topic_id, :nome, :descricao, :capa)";
                $stmt = $pdo->prepare($sql);
                $stmt->bindValue(':topic_id', $topico_id, PDO::PARAM_INT);
                $stmt->bindValue(':nome', $subtopico, PDO::PARAM_STR);
                $stmt->bindValue(':descricao', $descricao, PDO::PARAM_STR);
                $stmt->bindValue(':capa', $capa, PDO::PARAM_STR);
                $result = $stmt->execute();
                
                if ($result) {
                    $mensagem_subtopic = "Subtópico '$subtopico' criado com sucesso!";
                } else {
                    $erro_subtopic = "Erro ao criar o subtópico!";
                }
            }
            break;
            
        case 'delete':
            if (!empty($_POST['subtopico_id'])) {
                $subtopico_id = (int)$_POST['subtopico_id'];
                $sql = "DELETE FROM subtopics WHERE id = :id";
                $stmt = $pdo->prepare($sql);
                $stmt->bindValue(':id', $subtopico_id, PDO::PARAM_INT);
                $result = $stmt->execute();
                
                if ($result) {
                    $mensagem_subtopic = "Subtópico excluído com sucesso!";
                } else {
                    $erro_subtopic = "Erro ao excluir o subtópico!";
                }
            }
            break;
    }
}

// Função para limitar caracteres da descrição
function limitDescription($text, $limit = 100) {
    if (strlen($text) > $limit) {
        return substr($text, 0, $limit) . '...';
    }
    return $text;
}

// Obter informações do tópico
$topic_info = getTopicInfo($pdo, $topico_id);

// Obter subtópicos do tópico com paginação
$sql_count = "SELECT COUNT(*) FROM subtopics WHERE topic_id = :topic_id";
$stmt_count = $pdo->prepare($sql_count);
$stmt_count->bindValue(':topic_id', $topico_id, PDO::PARAM_INT);
$stmt_count->execute();
$total_subtopics = $stmt_count->fetchColumn();
$total_pages = ceil($total_subtopics / $subtopics_per_page);

$sql = "SELECT * FROM subtopics WHERE topic_id = :topic_id ORDER BY nome ASC LIMIT :limit OFFSET :offset";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':topic_id', $topico_id, PDO::PARAM_INT);
$stmt->bindValue(':limit', $subtopics_per_page, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$subtopics = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

        <!-- Coluna Principal -->
        <div class="column-main">
            <h1><?php echo htmlspecialchars($topic_info['nome']); ?></h1>
            <p><?php echo htmlspecialchars($topic_info['descricao']); ?></p>
            
            <?php if ($mensagem_subtopic): ?>
                <div style="background-color: #d4edda; color: #155724; padding: 10px; margin-bottom: 15px; border-radius: 4px;">
                    <?php echo htmlspecialchars($mensagem_subtopic); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($erro_subtopic): ?>
                <div style="background-color: #f8d7da; color: #721c24; padding: 10px; margin-bottom: 15px; border-radius: 4px;">
                    <?php echo htmlspecialchars($erro_subtopic); ?>
                </div>
            <?php endif; ?>
            
            <!-- Formulário para criar subtópico - APENAS PARA ADMINS -->
            <?php if (isAdmin()): ?>
                <div style="background-color: #ffffffff; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                    <h2>Criar Novo Subtópico</h2>
                    <form method="post" action="" enctype="multipart/form-data">
                        <input type="hidden" name="action_subtopic" value="create">
                        <input type="hidden" name="topico_id" value="<?php echo $topico_id; ?>">
                        <div style="margin-bottom: 10px;">
                            <label for="subtopico" style="display: block; margin-bottom: 5px;">Nome do Subtópico:</label>
                            <input type="text" id="subtopico" name="subtopico" required style="width: 100%; padding: 8px; border: 1px solid #000000ff; border-radius: 4px;">
                        </div>
                        <div style="margin-bottom: 10px;">
                            <label for="capa" style="display: block; margin-bottom: 5px;">Capa (Imagem):</label>
                            <input type="file" id="capa" name="capa" accept="image/*" style="width: 100%; padding: 8px; border: 1px solid #000000ff; border-radius: 4px;">
                            <small>Formatos aceitos: JPG, PNG, WEBP, GIF</small>
                        </div>
                        <div style="margin-bottom: 10px;">
                            <label for="descricao" style="display: block; margin-bottom: 5px;">Descrição:</label>
                            <textarea id="descricao" name="descricao" rows="3" style="width: 100%; padding: 8px; border: 1px solid #000000ff; border-radius: 4px;" maxlength="100"></textarea>
                            <small>Máximo 100 caracteres</small>
                        </div>
                        <button type="submit" style="background-color: #28a745; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer;">Criar Subtópico</button>
                    </form>
                </div>
            <?php endif; ?>
            
            <!-- Lista de subtópicos com cards clicáveis -->
            <h2>Subtópicos</h2>
            <?php if (empty($subtopics)): ?>
                <p>Nenhum subtópico encontrado para este tópico.</p>
            <?php else: ?>
                <div class="posts-list">
                    <?php foreach ($subtopics as $subtopic): ?>
                        <!-- Card inteiro clicável -->
                        <div class="post-item" style="margin-bottom: 1px; padding-bottom: 1px; border-bottom: 2px solid #eee; position: relative; cursor: pointer; box-shadow: 0px 1px 1px rgba(0,0,0,0.1)" 
                             onclick="window.location='subtopic.php?subtopic_id=<?php echo $subtopic['id']; ?>'">
                            
                            <div style="display: flex; align-items: flex-start;">
                                <div style="flex: 1; position: relative; z-index: 2;">
                                    <h3 style="margin-bottom: 5px;">
                                        <a href="subtopic.php?subtopic_id=<?php echo $subtopic['id']; ?>" 
                                           style="color: #000000ff; text-decoration: none; font-family: 'Arial Black', Arial, sans-serif;">
                                            <?php echo htmlspecialchars($subtopic['nome']); ?>
                                        </a>
                                    </h3>
                                    <p style="color: #000000ff; margin-bottom: 8px; font-size: 0.95em; font-family: 'Italic';">
                                        <?php echo htmlspecialchars(limitDescription($subtopic['descricao'], 120)); ?>
                                    </p>
                                    
                                    <!-- Botões de editar e excluir - APENAS PARA ADMINS -->
                                    <?php if (isAdmin()): ?>
                                        <div style="margin-top: 5px;">
                                            <a href="edit_subtopic.php?subtopic_id=<?php echo $subtopic['id']; ?>&topico_id=<?php echo $topico_id; ?>" 
                                               style="background-color: #ffc107; color: #212529; padding: 4px 8px; text-decoration: none; border-radius: 4px; margin-right: 5px; font-size: 0.85em; position: relative; z-index: 3;">Editar</a>
                                            <form method="post" action="" style="display: inline;" 
                                                  onsubmit="return confirm('Ao excluir o subtópico, todos os posts criados dentro dele serão excluidos juntos. Deseja continuar?');" 
                                                  style="position: relative; z-index: 3;">
                                                <input type="hidden" name="action_subtopic" value="delete">
                                                <input type="hidden" name="subtopico_id" value="<?php echo $subtopic['id']; ?>">
                                                <button type="submit" 
                                                        style="background-color: #dc3545; color: white; padding: 4px 8px; border: none; border-radius: 4px; cursor: pointer; font-size: 0.85em;">Excluir Subtópico</button>
                                            </form>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- Capa do subtópico à direita -->
                                <div style="margin-left: 20px; flex-shrink: 0; position: relative; z-index: 2;">
                                    <?php if (!empty($subtopic['capa'])): ?>
                                        <img src="<?php echo htmlspecialchars($subtopic['capa']); ?>" 
                                             alt="Capa do Subtópico" 
                                             style="width: 200px; height: 120px; object-fit: cover; border-radius: 8px;">
                                    <?php else: ?>
                                        <div style="width: 200px; height: 120px; background-color: #5b7688ff; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 0.9em; text-align: center;">
                                            Sem Capa
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <!-- Link invisível que cobre todo o card -->
                            <a href="subtopic.php?subtopic_id=<?php echo $subtopic['id']; ?>" 
                               style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 1;"></a>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Paginação -->
                <?php if ($total_pages > 1): ?>
                    <div class="pagination" style="margin-top: 30px; text-align: center;">
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <?php if ($i == $page): ?>
                                <span class="current" style="display: inline-block; padding: 8px 16px; margin: 0 5px; background-color: #2c3e50; color: white; text-decoration: none; border-radius: 4px;"><?php echo $i; ?></span>
                            <?php else: ?>
                                <a href="?topico_id=<?php echo $topico_id; ?>&page=<?php echo $i; ?>" 
                                   style="display: inline-block; padding: 8px 16px; margin: 0 5px; background-color: #3498db; color: white; text-decoration: none; border-radius: 4px;"><?php echo $i; ?></a>
                            <?php endif; ?>
                        <?php endfor; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>

        <!-- Coluna Lateral - Removida -->
    </div>
</body>
</html>