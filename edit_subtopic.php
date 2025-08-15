<?php
require_once 'includes/header.php';
require_once 'includes/auth.php'; 

if (!isset($_GET['subtopic_id']) || !isset($_GET['topico_id']) || 
    !is_numeric($_GET['subtopic_id']) || !is_numeric($_GET['topico_id'])) {
    die("IDs inválidos");
}

$subtopic_id = (int)$_GET['subtopic_id'];
$topico_id = (int)$_GET['topico_id'];

if (!isAdmin()) {
    die("Acesso negado! Apenas administradores podem editar subtópicos.");
}

// Obter informações do subtópico
$sql = "SELECT * FROM subtopics WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':id', $subtopic_id, PDO::PARAM_INT);
$stmt->execute();
$subtopic = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$subtopic) {
    die("Subtópico não encontrado");
}

$mensagem = '';
$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['subtopico_novo'])) {
        $subtopico_novo = trim($_POST['subtopico_novo']);
        $descricao_nova = trim($_POST['descricao_nova']);
        $capa_nova = $subtopic['capa']; // Manter a capa atual por padrão
        
        // Tratar upload de nova capa se houver
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
                    // Se tinha capa antiga, remover o arquivo
                    if (!empty($subtopic['capa']) && file_exists($subtopic['capa'])) {
                        unlink($subtopic['capa']);
                    }
                    $capa_nova = $upload_path;
                }
            }
        }
        
        // Atualizar o subtópico
        $sql_update = "UPDATE subtopics SET nome = :nome, descricao = :descricao, capa = :capa WHERE id = :id";
        $stmt_update = $pdo->prepare($sql_update);
        $stmt_update->bindValue(':id', $subtopic_id, PDO::PARAM_INT);
        $stmt_update->bindValue(':nome', $subtopico_novo, PDO::PARAM_STR);
        $stmt_update->bindValue(':descricao', $descricao_nova, PDO::PARAM_STR);
        $stmt_update->bindValue(':capa', $capa_nova, PDO::PARAM_STR);
        
        if ($stmt_update->execute()) {
            $mensagem = "Subtópico atualizado com sucesso!";
            // Atualizar os dados do subtópico
            $sql = "SELECT * FROM subtopics WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':id', $subtopic_id, PDO::PARAM_INT);
            $stmt->execute();
            $subtopic = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            $erro = "Erro ao atualizar o subtópico!";
        }
    } else {
        $erro = "Nome do subtópico e descrição são obrigatórios!";
    }
}
?>

        <!-- Coluna Principal -->
        <div class="column-main">
            <h1>Editar Subtópico</h1>
            <h2><?php echo htmlspecialchars($subtopic['nome']); ?></h2>
            
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
                <form method="post" action="" enctype="multipart/form-data">
                    <div style="margin-bottom: 10px;">
                        <label for="subtopico_novo" style="display: block; margin-bottom: 5px;">Nome do Subtópico:</label>
                        <input type="text" id="subtopico_novo" name="subtopico_novo" value="<?php echo htmlspecialchars($subtopic['nome']); ?>" required style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                    </div>
                    
                    <div style="margin-bottom: 10px;">
                        <label for="descricao_nova" style="display: block; margin-bottom: 5px;">Descrição:</label>
                        <textarea id="descricao_nova" name="descricao_nova" rows="3" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;"><?php echo htmlspecialchars($subtopic['descricao']); ?></textarea>
                    </div>
                    
                    <div style="margin-bottom: 10px;">
                        <label for="capa" style="display: block; margin-bottom: 5px;">Capa (Imagem):</label>
                        <input type="file" id="capa" name="capa" accept="image/*" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                        <small>Formatos aceitos: JPG, PNG, WEBP, GIF</small>
                        <?php if (!empty($subtopic['capa'])): ?>
                            <div style="margin-top: 10px;">
                                <p>Capa atual:</p>
                                <img src="<?php echo htmlspecialchars($subtopic['capa']); ?>" alt="Capa atual" style="max-width: 200px; height: auto; border-radius: 4px;">
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <button type="submit" style="background-color: #28a745; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer;">Atualizar Subtópico</button>
                    <a href="topic.php?topico_id=<?php echo $topico_id; ?>" style="background-color: #6c757d; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; margin-left: 10px;">Voltar</a>
                </form>
            </div>
        </div>

        <!-- Coluna Lateral - Removida -->
    </div>
</body>
</html>