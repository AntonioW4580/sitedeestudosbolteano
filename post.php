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

function cleanTextSpacing($text) {
    // Remover múltiplos espaços consecutivos
    $text = preg_replace('/\s+/', ' ', $text);
    
    // Remover &nbsp; (espaço duro)
    $text = str_replace('&nbsp;', ' ', $text);
    
    // Remover espaços no início e fim
    $text = trim($text);
    
    // Remover linhas vazias duplicadas
    $text = preg_replace('/\n\s*\n\s*\n/', "\n\n", $text);
    
    // Normalizar quebras de linha
    $text = str_replace(["\r\n", "\r"], "\n", $text);
    
    return $text;
}

// Limpar espaçamento do texto do post
$post['texto'] = cleanTextSpacing($post['texto']);
$post['descricao'] = cleanTextSpacing($post['descricao']);
$post['titulo'] = cleanTextSpacing($post['titulo']);
?>

        <!-- Coluna Principal -->
        <div class="column-main">
            <div class="single-post">
                <h1 style="text-align: center; margin-bottom: 20px; font-family: 'Crimson Text', Georgia, serif; font-weight: normal;"><?php echo htmlspecialchars($post['titulo']); ?></h1>
                
                <!-- Descrição do post -->
                <?php if (isset($post['descricao']) && !empty($post['descricao'])): ?>
                    <div style="text-align: center; margin-bottom: 30px; color: #555; font-size: 1.4em; font-family: 'Crimson Text', Georgia, serif; font-style: italic; padding: 0 20px;">
                        <?php echo nl2br(htmlspecialchars($post['descricao'])); ?>
                    </div>
                <?php endif; ?>
                
                <!-- Capa REMOVIDA conforme solicitado -->
                
                <div class="post-meta" style="text-align: center; margin-bottom: 30px; color: #7f8c8d; font-size: 1.1em; font-family: 'Crimson Text', Georgia, serif;">
                    Publicado em: <?php echo date('d/m/Y H:i', strtotime($post['data_criacao'])); ?><br>
                    Tópico: <?php echo htmlspecialchars($post['topic_nome']); ?><br>
                    Subtópico: <?php echo htmlspecialchars($post['subtopic_nome']); ?>
                </div>
                <div class="post-content" style="max-width: 800px; margin: 0 auto; text-align: justify; line-height: 1.8; font-size: 1.3em; font-family: 'Crimson Text', Georgia, serif;">
                    <?php 
                    // Centralizar imagens e definir tamanho padrão (870x382)
                    echo preg_replace_callback('/<img(.*?)>/i', function ($matches) {
                        // Extrair atributos existentes
                        $attrs = $matches[1];
                        
                        // Remover width e height se existirem
                        $attrs = preg_replace('/width\s*=\s*["\'][^"\']*["\']/', '', $attrs);
                        $attrs = preg_replace('/height\s*=\s*["\'][^"\']*["\']/', '', $attrs);
                        
                        // Adicionar estilo para tamanho fixo e centralização
                        return '<div style="text-align: center; margin: 25px 0;"><img' . $attrs . ' style="max-width: 870px; max-height: 382px; object-fit: contain; display: inline-block;" /></div>';
                    }, $post['texto']);
                    ?>
                </div>
            </div>
        </div>

        <!-- Coluna Lateral - Removida -->
    </div>
</body>
</html>