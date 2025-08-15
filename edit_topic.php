<?php
require_once 'includes/header.php';
require_once 'includes/auth.php';

if (!isAdmin()) {
    header("Location: index.php");
    exit();
}

if (!isset($_GET['topico_id']) || empty($_GET['topico_id'])) {
    die("Tópico inválido");
}

$topico_id = (int)$_GET['topico_id'];

function getTopicInfo($pdo, $topico_id) {
    $sql = "SELECT * FROM topics WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':id', $topico_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

$mensagem = '';
$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['topico_novo'])) {
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
            // Atualizar as informações do tópico
            $topic_info = getTopicInfo($pdo, $topico_id);
        } catch (Exception $e) {
            $erro = "Erro ao atualizar o tópico!";
        }
    } else {
        $erro = "Nome do tópico e descrição são obrigatórios!";
    }
}

$topic_info = getTopicInfo($pdo, $topico_id);

if (!$topic_info) {
    die("Tópico não encontrado");
}
?>

        <!-- Coluna Principal -->
        <div class="column-main">
            <h1>Editar Tópico</h1>
            <h2><?php echo htmlspecialchars($topic_info['nome']); ?></h2>
            
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
            
            <!-- Formulário de edição -->
            <div style="background-color: #f8f9fa; padding: 20px; border-radius: 8px;">
                <form method="post" action="">
                    <div style="margin-bottom: 10px;">
                        <label for="topico_novo" style="display: block; margin-bottom: 5px;">Nome do Tópico:</label>
                        <input type="text" id="topico_novo" name="topico_novo" value="<?php echo htmlspecialchars($topic_info['nome']); ?>" required style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                    </div>
                    
                    <div style="margin-bottom: 10px;">
                        <label for="descricao_nova" style="display: block; margin-bottom: 5px;">Descrição:</label>
                        <textarea id="descricao_nova" name="descricao_nova" rows="3" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;"><?php echo htmlspecialchars($topic_info['descricao']); ?></textarea>
                    </div>
                    
                    <button type="submit" style="background-color: #28a745; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer;">Atualizar Tópico</button>
                    <a href="admin.php" style="background-color: #6c757d; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; margin-left: 10px;">Voltar</a>
                </form>
            </div>
        </div>

        <!-- Coluna Lateral - Removida -->
    </div>
</body>
</html>