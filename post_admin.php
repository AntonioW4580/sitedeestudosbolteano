<?php
require_once 'includes/header.php';
require_once 'includes/auth.php';

if (!isset($_GET['subtopic_id']) || empty($_GET['subtopic_id'])) {
    die("Subtópico inválido");
}

$subtopic_id = (int)$_GET['subtopic_id'];

$sql = "SELECT s.*, t.nome as topic_nome FROM subtopics s 
        JOIN topics t ON s.topic_id = t.id 
        WHERE s.id = :id";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':id', $subtopic_id, PDO::PARAM_INT);
$stmt->execute();
$subtopic_info = $stmt->fetch(PDO::FETCH_ASSOC);

// Processar requisições POST para posts
$mensagem_post = '';
$erro_post = '';

// Verificar se é uma requisição POST para posts E usuário é admin
if (isAdmin() && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_post'])) {
    switch ($_POST['action_post']) {
        case 'create':
            if (!empty($_POST['titulo'])) {
                $titulo = trim($_POST['titulo']);
                $descricao = !empty($_POST['descricao']) ? trim($_POST['descricao']) : '';
                $texto = !empty($_POST['texto']) ? trim($_POST['texto']) : '';
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
                
                // Campos removidos conforme solicitado
                $imagens = [];
                $links = [];
                $hashtags = [];
                
                $result = createPost($pdo, $subtopic_id, $titulo, $descricao, $texto, $capa, $imagens, $links, $hashtags);
                if ($result) {
                    $mensagem_post = "Post '$titulo' criado com sucesso!";
                } else {
                    $erro_post = "Erro ao criar o post!";
                }
            } else {
                $erro_post = "Título é obrigatório!";
            }
            break;
    }
}
?>

        <!-- Coluna Principal -->
        <div class="column-main">
            
            <?php if ($mensagem_post): ?>
                <div style="background-color: #d4edda; color: #155724; padding: 10px; margin-bottom: 15px; border-radius: 4px;">
                    <?php echo htmlspecialchars($mensagem_post); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($erro_post): ?>
                <div style="background-color: #f8d7da; color: #721c24; padding: 10px; margin-bottom: 15px; border-radius: 4px;">
                    <?php echo htmlspecialchars($erro_post); ?>
                </div>
            <?php endif; ?>
            
            <!-- Formulário para criar post -->
            <div style="background-color: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                <h2>Criar Novo Post</h2>
                <form method="post" action="" enctype="multipart/form-data" id="postForm">
                    <input type="hidden" name="action_post" value="create">
                    <input type="hidden" name="subtopic_id" value="<?php echo $subtopic_id; ?>">
                    
                    <div style="margin-bottom: 10px;">
                        <label for="titulo" style="display: block; margin-bottom: 5px;">Título:</label>
                        <input type="text" id="titulo" name="titulo" required style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                    </div>
                    
                    <div style="margin-bottom: 10px;">
                        <label for="capa" style="display: block; margin-bottom: 5px;">Capa (Imagem):</label>
                        <input type="file" id="capa" name="capa" accept="image/*" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                        <small>Formatos aceitos: JPG, PNG, WEBP, GIF</small>
                    </div>
                    
                    <div style="margin-bottom: 10px;">
                        <label for="descricao" style="display: block; margin-bottom: 5px;">Descrição:</label>
                        <textarea id="descricao" name="descricao" rows="3" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;"></textarea>
                    </div>
                    
                    <div style="margin-bottom: 10px;">
                        <label for="texto" style="display: block; margin-bottom: 5px;">Texto Completo:</label>
                        
                        <!-- Toolbar do editor simples -->
                        <div id="toolbar" style="border: 1px solid #ddd; border-bottom: none; padding: 5px; background: #f0f0f0;">
                            <button type="button" onclick="execCommand('bold')" title="Negrito" style="font-weight: bold; padding: 5px; margin: 2px;">B</button>
                            <button type="button" onclick="execCommand('italic')" title="Itálico" style="font-style: italic; padding: 5px; margin: 2px;">I</button>
                            <button type="button" onclick="execCommand('underline')" title="Sublinhado" style="text-decoration: underline; padding: 5px; margin: 2px;">U</button>
                            <button type="button" onclick="execCommand('insertUnorderedList')" title="Lista" style="padding: 5px; margin: 2px;">•</button>
                            <button type="button" onclick="insertLink()" title="Link" style="padding: 5px; margin: 2px;">🔗</button>
                            <button type="button" onclick="insertImageUrl()" title="Inserir Imagem por URL" style="padding: 5px; margin: 2px;">🖼️</button>
                        </div>
                        
                        <!-- Editor de texto -->
                        <div id="editor" contenteditable="true" style="width: 100%; min-height: 300px; border: 1px solid #ddd; padding: 10px; background: white;"></div>
                        
                        <!-- Textarea oculta para enviar o conteúdo -->
                        <textarea id="texto" name="texto" style="display: none;"></textarea>
                    </div>
                    
                    <button type="submit" style="background-color: #28a745; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer;">Criar Post</button>
                </form>
            </div>
        </div>

        <!-- Coluna Lateral - Removida -->
    </div>
    
    <script>
        // Sincronizar o editor com o textarea antes do envio
        document.getElementById('postForm').addEventListener('submit', function() {
            document.getElementById('texto').value = document.getElementById('editor').innerHTML;
        });
        
        // Funções do editor
        function execCommand(command) {
            document.getElementById('editor').focus();
            document.execCommand(command, false, null);
        }
        
        function insertLink() {
            const url = prompt('Digite a URL:');
            if (url) {
                const selection = window.getSelection();
                if (selection.toString()) {
                    document.execCommand('createLink', false, url);
                } else {
                    const linkText = prompt('Digite o texto do link:');
                    if (linkText) {
                        document.execCommand('insertHTML', false, `<a href="${url}">${linkText}</a>`);
                    }
                }
            }
        }
        
        function insertImageUrl() {
            const url = prompt('Digite a URL da imagem:');
            if (url) {
                document.execCommand('insertImage', false, url);
            }
        }
    </script>
</body>
</html>