<?php
// Handler para upload de imagens
function handleImageUpload($file_input_name) {
    if (!isset($_FILES[$file_input_name]) || $_FILES[$file_input_name]['error'] == UPLOAD_ERR_NO_FILE) {
        return '';
    }
    
    $upload_dir = 'uploads/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    $file = $_FILES[$file_input_name];
    
    // Verificar se houve erro no upload
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return '';
    }
    
    // Verificar tipo de arquivo
    $allowed_types = ['image/jpeg', 'image/png', 'image/webp'];
    $file_type = mime_content_type($file['tmp_name']);
    
    if (!in_array($file_type, $allowed_types)) {
        return '';
    }
    
    // Gerar nome único para o arquivo
    $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $new_filename = uniqid() . '.' . $file_extension;
    $upload_path = $upload_dir . $new_filename;
    
    // Mover arquivo para o diretório de uploads
    if (move_uploaded_file($file['tmp_name'], $upload_path)) {
        return $upload_path;
    }
    
    return '';
}
?>