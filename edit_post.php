<?php
require_once 'includes/header.php';
require_once 'includes/auth.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID inválido");
}

$post_id = (int)$_GET['id'];

$post = getPostById($pdo, $post_id);

if (!$post) {
    die("Post não encontrado");
}

$mensagem = '';
$erro = '';

if (isAdmin() && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['titulo'])) {
        $titulo = trim($_POST['titulo']);
        $descricao = !empty($_POST['descricao']) ? trim($_POST['descricao']) : '';
        $texto = !empty($_POST['texto']) ? trim($_POST['texto']) : '';
        $capa = !empty($_POST['capa']) ? trim($_POST['capa']) : '';
        
        // Campos removidos conforme solicitado
        $imagens = [];
        $links = [];
        $hashtags = [];
        
        $result = updatePost($pdo, $post_id, $post['subtopic_id'], $titulo, $descricao, $texto, $capa, $imagens, $links, $hashtags);
        if ($result) {
            $mensagem = "Post atualizado com sucesso!";
            // Atualizar o post para mostrar os novos dados
            $post = getPostById($pdo, $post_id);
        } else {
            $erro = "Erro ao atualizar o post!";
        }
    } else {
        $erro = "Título é obrigatório!";
    }
}
?>

        <!-- Coluna Principal -->
        <div class="column-main">
            <h1>Editar Post</h1>
            <h2><?php echo htmlspecialchars($post['titulo']); ?></h2>
            
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
                <form method="post" action="" id="editForm">
                    <div style="margin-bottom: 10px;">
                        <label for="titulo" style="display: block; margin-bottom: 5px;">Título:</label>
                        <input type="text" id="titulo" name="titulo" value="<?php echo htmlspecialchars($post['titulo']); ?>" required style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                    </div>
                    
                    <div style="margin-bottom: 10px;">
                        <label for="capa" style="display: block; margin-bottom: 5px;">Capa (URL):</label>
                        <input type="text" id="capa" name="capa" value="<?php echo htmlspecialchars($post['capa']); ?>" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                    </div>
                    
                    <div style="margin-bottom: 10px;">
                        <label for="descricao" style="display: block; margin-bottom: 5px;">Descrição:</label>
                        <textarea id="descricao" name="descricao" rows="3" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;"><?php echo htmlspecialchars($post['descricao']); ?></textarea>
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
                        <div id="editor" contenteditable="true" style="width: 100%; min-height: 300px; border: 1px solid #ddd; padding: 10px; background: white;"><?php echo $post['texto']; ?></div>
                        
                        <!-- Textarea oculta para enviar o conteúdo -->
                        <textarea id="texto" name="texto" style="display: none;"></textarea>
                    </div>
                    
                    <button type="submit" style="background-color: #28a745; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer;">Atualizar Post</button>
                    <a href="subtopic.php?subtopic_id=<?php echo $post['subtopic_id']; ?>" style="background-color: #6c757d; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; margin-left: 10px;">Voltar</a>
                </form>
            </div>
        </div>

        <!-- Coluna Lateral - Removida pela IA VAGABUNDA -->
    </div>

    <script>
        document.getElementById('editForm').addEventListener('submit', function() {
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
    
    <script>
        // Sincronizar o editor com o textarea antes do envio
        document.getElementById('editForm').addEventListener('submit', function() {
            document.getElementById('texto').value = document.getElementById('editor').innerHTML;
        });
        
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
                const altText = prompt('Digite uma descrição para a imagem (opcional):');
                let imgTag = `<img src="${url}"`;
                if (altText) {
                    imgTag += ` alt="${altText}"`;
                }
                imgTag += ' />';
                document.execCommand('insertHTML', false, imgTag);
            }
        }
        
        function showImageUpload() {
            document.getElementById('imageUploadModal').style.display = 'block';
            document.getElementById('imageFile').value = '';
            document.getElementById('imageAlt').value = '';
        }
        
        function closeImageModal() {
            document.getElementById('imageUploadModal').style.display = 'none';
            document.getElementById('imageFile').value = '';
            document.getElementById('imageAlt').value = '';
        }
        
        function uploadImage() {
            const fileInput = document.getElementById('imageFile');
            const altInput = document.getElementById('imageAlt');
            const file = fileInput.files[0];
            const altText = altInput.value;
            
            if (!file) {
                alert('Selecione uma imagem primeiro!');
                return;
            }
            
            const formData = new FormData();
            formData.append('action_post', 'upload_image');
            formData.append('image', file);
            
            fetch('uploadhandler2.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erro na resposta: ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                if (data.url) {
                    let imgTag = `<img src="${data.url}"`;
                    if (altText) {
                        imgTag += ` alt="${altText}"`;
                    }
                    imgTag += ' />';
                    document.execCommand('insertHTML', false, imgTag);
                    closeImageModal();
                } else {
                    alert('Erro ao fazer upload da imagem: ' + (data.error || 'Erro desconhecido'));
                }
            })
            .catch(error => {
                alert('Erro na requisição: ' + error.message);
                closeImageModal();
            });
        }
        
        window.onclick = function(event) {
            const modal = document.getElementById('imageUploadModal');
            if (event.target === modal) {
                closeImageModal();
            }
        }
    </script>
</body>
</html>