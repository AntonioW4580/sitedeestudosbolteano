<?php
$host = 'localhost';
$dbname = 'meusite';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password, [
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
    ]);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    
    
    $pdo->exec("SET NAMES utf8mb4");
    $pdo->exec("SET CHARACTER SET utf8mb4");
    $pdo->exec("SET character_set_connection=utf8mb4");
    $pdo->exec("SET character_set_client=utf8mb4");
    $pdo->exec("SET character_set_results=utf8mb4");
} catch(PDOException $e) {
    die("Erro na conexão com o banco de dados: " . $e->getMessage());
}

function getTopics($pdo) {
    $sql = "SELECT * FROM topics ORDER BY nome ASC";
    $stmt = $pdo->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getSubtopicsByTopic($pdo, $topic_id) {
    $sql = "SELECT * FROM subtopics WHERE topic_id = :topic_id ORDER BY nome ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':topic_id', $topic_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getPostsBySubtopic($pdo, $subtopic_id, $limit = 5, $offset = 0) {
    $sql = "SELECT * FROM posts WHERE subtopic_id = :subtopic_id ORDER BY data_criacao DESC LIMIT :limit OFFSET :offset";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':subtopic_id', $subtopic_id, PDO::PARAM_INT);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getTotalPostsBySubtopic($pdo, $subtopic_id) {
    $sql = "SELECT COUNT(*) FROM posts WHERE subtopic_id = :subtopic_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':subtopic_id', $subtopic_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchColumn();
}

// Função para obter posts recentes (todos os posts) ordenados por data decrescente
function getRecentPosts($pdo, $limit = 5, $offset = 0) {
    $sql = "SELECT p.*, s.nome as subtopic_nome, t.nome as topic_nome FROM posts p 
            JOIN subtopics s ON p.subtopic_id = s.id 
            JOIN topics t ON s.topic_id = t.id 
            ORDER BY p.data_criacao DESC 
            LIMIT :limit OFFSET :offset";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getTotalRecentPosts($pdo) {
    $sql = "SELECT COUNT(*) FROM posts";
    $stmt = $pdo->query($sql);
    return $stmt->fetchColumn();
}

function getAllPostsForSidebar($pdo) {
    $sql = "SELECT p.id, p.titulo, s.nome as subtopic_nome, t.nome as topic_nome FROM posts p 
            JOIN subtopics s ON p.subtopic_id = s.id 
            JOIN topics t ON s.topic_id = t.id 
            ORDER BY t.nome ASC, s.nome ASC, p.titulo ASC";
    $stmt = $pdo->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getPostById($pdo, $id) {
    $sql = "SELECT p.*, s.nome as subtopic_nome, t.nome as topic_nome FROM posts p 
            JOIN subtopics s ON p.subtopic_id = s.id 
            JOIN topics t ON s.topic_id = t.id 
            WHERE p.id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Função para criar tópico
function createTopic($pdo, $nome, $descricao = '') {
    try {
        $sql = "INSERT INTO topics (nome, descricao) VALUES (:nome, :descricao)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':nome', $nome, PDO::PARAM_STR);
        $stmt->bindValue(':descricao', $descricao, PDO::PARAM_STR);
        $stmt->execute();
        return $pdo->lastInsertId();
    } catch (Exception $e) {
        return false;
    }
}

function createSubtopic($pdo, $topic_id, $nome, $descricao = '', $capa = '') {
    try {
        $sql = "INSERT INTO subtopics (topic_id, nome, descricao, capa) VALUES (:topic_id, :nome, :descricao, :capa)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':topic_id', $topic_id, PDO::PARAM_INT);
        $stmt->bindValue(':nome', $nome, PDO::PARAM_STR);
        $stmt->bindValue(':descricao', $descricao, PDO::PARAM_STR);
        $stmt->bindValue(':capa', $capa, PDO::PARAM_STR);
        $stmt->execute();
        return $pdo->lastInsertId();
    } catch (Exception $e) {
        return false;
    }
}

function createPost($pdo, $subtopic_id, $titulo, $descricao, $texto, $capa = '', $imagens = [], $links = [], $hashtags = []) {
    try {
        $sql = "INSERT INTO posts (subtopic_id, titulo, descricao, texto, capa, imagens, links, hashtags) VALUES (:subtopic_id, :titulo, :descricao, :texto, :capa, :imagens, :links, :hashtags)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':subtopic_id', $subtopic_id, PDO::PARAM_INT);
        $stmt->bindValue(':titulo', $titulo, PDO::PARAM_STR);
        $stmt->bindValue(':descricao', $descricao, PDO::PARAM_STR);
        $stmt->bindValue(':texto', $texto, PDO::PARAM_STR);
        $stmt->bindValue(':capa', $capa, PDO::PARAM_STR);
        $stmt->bindValue(':imagens', serialize($imagens), PDO::PARAM_STR);
        $stmt->bindValue(':links', serialize($links), PDO::PARAM_STR);
        $stmt->bindValue(':hashtags', serialize($hashtags), PDO::PARAM_STR);
        $stmt->execute();
        return $pdo->lastInsertId();
    } catch (Exception $e) {
        return false;
    }
}

function deleteTopic($pdo, $topic_id) {
    try {
        $pdo->beginTransaction();
        
        // Deletar posts do subtópico
        $sql_posts = "DELETE FROM posts WHERE subtopic_id IN (SELECT id FROM subtopics WHERE topic_id = :topic_id)";
        $stmt_posts = $pdo->prepare($sql_posts);
        $stmt_posts->bindValue(':topic_id', $topic_id, PDO::PARAM_INT);
        $stmt_posts->execute();
        
        // Deletar subtópicos
        $sql_subtopics = "DELETE FROM subtopics WHERE topic_id = :topic_id";
        $stmt_subtopics = $pdo->prepare($sql_subtopics);
        $stmt_subtopics->bindValue(':topic_id', $topic_id, PDO::PARAM_INT);
        $stmt_subtopics->execute();
        
        // Deletar tópico
        $sql_topic = "DELETE FROM topics WHERE id = :topic_id";
        $stmt_topic = $pdo->prepare($sql_topic);
        $stmt_topic->bindValue(':topic_id', $topic_id, PDO::PARAM_INT);
        $stmt_topic->execute();
        
        $pdo->commit();
        return true;
    } catch (Exception $e) {
        $pdo->rollback();
        return false;
    }
}

function deleteSubtopic($pdo, $subtopic_id) {
    try {
        $pdo->beginTransaction();
        
        // Deletar posts do subtópico
        $sql_posts = "DELETE FROM posts WHERE subtopic_id = :subtopic_id";
        $stmt_posts = $pdo->prepare($sql_posts);
        $stmt_posts->bindValue(':subtopic_id', $subtopic_id, PDO::PARAM_INT);
        $stmt_posts->execute();
        
        // Deletar subtópico
        $sql_subtopic = "DELETE FROM subtopics WHERE id = :subtopic_id";
        $stmt_subtopic = $pdo->prepare($sql_subtopic);
        $stmt_subtopic->bindValue(':subtopic_id', $subtopic_id, PDO::PARAM_INT);
        $stmt_subtopic->execute();
        
        $pdo->commit();
        return true;
    } catch (Exception $e) {
        $pdo->rollback();
        return false;
    }
}

function deletePost($pdo, $post_id) {
    try {
        $sql = "DELETE FROM posts WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id', $post_id, PDO::PARAM_INT);
        $stmt->execute();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

function updateTopic($pdo, $topic_id, $nome, $descricao) {
    try {
        $sql = "UPDATE topics SET nome = :nome, descricao = :descricao WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id', $topic_id, PDO::PARAM_INT);
        $stmt->bindValue(':nome', $nome, PDO::PARAM_STR);
        $stmt->bindValue(':descricao', $descricao, PDO::PARAM_STR);
        $stmt->execute();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

function updateSubtopic($pdo, $subtopic_id, $nome, $descricao, $capa = '') {
    try {
        $sql = "UPDATE subtopics SET nome = :nome, descricao = :descricao, capa = :capa WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id', $subtopic_id, PDO::PARAM_INT);
        $stmt->bindValue(':nome', $nome, PDO::PARAM_STR);
        $stmt->bindValue(':descricao', $descricao, PDO::PARAM_STR);
        $stmt->bindValue(':capa', $capa, PDO::PARAM_STR);
        $stmt->execute();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

// Função para atualizar post (com suporte a emojis)

// Função para atualizar post (COM DEBUGGING)
function updatePost($pdo, $post_id, $subtopic_id, $titulo, $descricao, $texto, $capa = '', $imagens = [], $links = [], $hashtags = []) {
    try {
        // Debug: Mostrar dados recebidos
        error_log("DEBUG UPDATE POST - ID: $post_id, Titulo: $titulo, Capa: $capa");
        
        // Primeiro obter o post atual para verificar a capa existente
        $sql_get = "SELECT capa FROM posts WHERE id = :id";
        $stmt_get = $pdo->prepare($sql_get);
        $stmt_get->bindValue(':id', $post_id, PDO::PARAM_INT);
        $stmt_get->execute();
        $post_atual = $stmt_get->fetch(PDO::FETCH_ASSOC);
        
        // Debug: Mostrar capa atual
        error_log("DEBUG UPDATE POST - Capa atual: " . ($post_atual ? $post_atual['capa'] : 'Nenhuma'));
        
        // Se não houver nova capa, manter a capa existente
        if (empty($capa) && $post_atual && !empty($post_atual['capa'])) {
            $capa = $post_atual['capa'];
        }
        
        // Debug: Capa final a ser usada
        error_log("DEBUG UPDATE POST - Capa final: $capa");
        
        $sql = "UPDATE posts SET subtopic_id = :subtopic_id, titulo = :titulo, descricao = :descricao, texto = :texto, capa = :capa WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id', $post_id, PDO::PARAM_INT);
        $stmt->bindValue(':subtopic_id', $subtopic_id, PDO::PARAM_INT);
        $stmt->bindValue(':titulo', $titulo, PDO::PARAM_STR);
        $stmt->bindValue(':descricao', $descricao, PDO::PARAM_STR);
        $stmt->bindValue(':texto', $texto, PDO::PARAM_STR);
        $stmt->bindValue(':capa', $capa, PDO::PARAM_STR);
        // Removido campos não usados: imagens, links, hashtags
        $result = $stmt->execute();
        
        error_log("DEBUG UPDATE POST - Resultado: " . ($result ? 'SUCESSO' : 'FALHA'));
        
        return $result;
    } catch (Exception $e) {
        error_log("DEBUG UPDATE POST - ERRO: " . $e->getMessage());
        return false;
    }
}
?>