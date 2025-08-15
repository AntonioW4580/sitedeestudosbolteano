<?php
require_once 'includes/header.php';
require_once 'includes/auth.php';

if (!isAdmin()) {
    header("Location: index.php");
    exit();
}

function getAllTopics($pdo) {
    $sql = "SELECT * FROM topics ORDER BY nome ASC";
    $stmt = $pdo->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$mensagem = '';
$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create':
                if (!empty($_POST['topico'])) {
                    $topico = trim($_POST['topico']);
                    $descricao = trim($_POST['descricao']);
                    
                    $sql = "INSERT INTO topics (nome, descricao) VALUES (:nome, :descricao)";
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindValue(':nome', $topico, PDO::PARAM_STR);
                    $stmt->bindValue(':descricao', $descricao, PDO::PARAM_STR);
                    
                    try {
                        $stmt->execute();
                        $mensagem = "Tópico '$topico' criado com sucesso!";
                    } catch (Exception $e) {
                        $erro = "Erro ao criar o tópico!";
                    }
                } else {
                    $erro = "Nome do tópico e descrição são obrigatórios!";
                }
                break;
                
            case 'delete':
                if (!empty($_POST['topico_id'])) {
                    $topico_id = (int)$_POST['topico_id'];
                    
                    // Obter nome do tópico antes de deletar
                    $sql_select = "SELECT nome FROM topics WHERE id = :id";
                    $stmt_select = $pdo->prepare($sql_select);
                    $stmt_select->bindValue(':id', $topico_id, PDO::PARAM_INT);
                    $stmt_select->execute();
                    $topic = $stmt_select->fetch(PDO::FETCH_ASSOC);
                    
                    if ($topic) {
                        $sql_delete = "DELETE FROM topics WHERE id = :id";
                        $stmt_delete = $pdo->prepare($sql_delete);
                        $stmt_delete->bindValue(':id', $topico_id, PDO::PARAM_INT);
                        
                        try {
                            $stmt_delete->execute();
                            $mensagem = "Tópico '{$topic['nome']}' excluído com sucesso!";
                        } catch (Exception $e) {
                            $erro = "Erro ao excluir o tópico!";
                        }
                    }
                }
                break;
                
            case 'edit':
                if (!empty($_POST['topico_id']) && !empty($_POST['topico_novo']) && !empty($_POST['descricao_nova'])) {
                    $topico_id = (int)$_POST['topico_id'];
                    $topico_novo = trim($_POST['topico_novo']);
                    $descricao_nova = trim($_POST['descricao_nova']);
                    
                    $sql = "UPDATE topics SET nome = :nome, descricao = :descricao WHERE id = :id";
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindValue(':id', $topico_id, PDO::PARAM_INT);
                    $stmt->bindValue(':nome', $topico_novo, PDO::PARAM_STR);
                    $stmt->bindValue(':descricao', $descricao_nova, PDO::PARAM_STR);
                    
                    try {
                        $stmt->execute();
                        $mensagem = "Tópico atualizado com sucesso!";
                    } catch (Exception $e) {
                        $erro = "Erro ao atualizar o tópico!";
                    }
                } else {
                    $erro = "Todos os campos são obrigatórios!";
                }
                break;
        }
    }
}

// Obter todos os tópicos
$topics = getAllTopics($pdo);
?>

        <!-- Coluna Principal -->
        <div class="column-main">
            <h1>Administração de Tópicos</h1>
            
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
            
            <!-- Formulário para criar tópico -->
            <div style="background-color: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                <h2>Criar Novo Tópico</h2>
                <form method="post" action="">
                    <input type="hidden" name="action" value="create">
                    <div style="margin-bottom: 10px;">
                        <label for="topico" style="display: block; margin-bottom: 5px;">Nome do Tópico:</label>
                        <input type="text" id="topico" name="topico" required style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                    </div>
                    <div style="margin-bottom: 10px;">
                        <label for="descricao" style="display: block; margin-bottom: 5px;">Descrição:</label>
                        <textarea id="descricao" name="descricao" rows="3" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;"></textarea>
                    </div>
                    <button type="submit" style="background-color: #28a745; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer;">Criar Tópico</button>
                </form>
            </div>
            
            <!-- Lista de tópicos com opções de edição e exclusão -->
            <h2>Tópicos Existentes</h2>
            <?php if (empty($topics)): ?>
                <p>Nenhum tópico encontrado.</p>
            <?php else: ?>
                <div class="posts-list">
                    <?php foreach ($topics as $topic): ?>
                        <div class="post-item" style="margin-bottom: 25px; padding-bottom: 20px; border-bottom: 1px solid #eee;">
                            <h3 style="margin-bottom: 10px;">
                                <a href="topic.php?topico_id=<?php echo $topic['id']; ?>" style="color: #2c3e50; text-decoration: none;">
                                    <?php echo htmlspecialchars($topic['nome']); ?>
                                </a>
                            </h3>
                            <p style="color: #666; margin-bottom: 15px;"><?php echo htmlspecialchars($topic['descricao']); ?></p>
                            
                            <!-- Botões de editar e excluir abaixo -->
                            <div style="margin-top: 10px;">
                                <a href="edit_topic.php?topico_id=<?php echo $topic['id']; ?>" style="background-color: #ffc107; color: #212529; padding: 6px 12px; text-decoration: none; border-radius: 4px; margin-right: 5px; font-size: 0.9em;">Editar</a>
                                <form method="post" action="" onsubmit="return confirm('Ao excluir o tópico, todos os sub-tópicos e posts criados serão excluidos juntos. Deseja continuar?');" style="display: inline;">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="topico_id" value="<?php echo $topic['id']; ?>">
                                    <button type="submit" style="background-color: #dc3545; color: white; padding: 6px 12px; border: none; border-radius: 4px; cursor: pointer; font-size: 0.9em;">Excluir Tópico</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Coluna Lateral - Removida -->
    </div>
</body>
</html>