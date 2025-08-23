<?php
header('Content-Type: application/json');

if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $upload_dir = 'uploads/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    $file = $_FILES['image'];
    $allowed_types = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
    $file_type = mime_content_type($file['tmp_name']);
    
    if (in_array($file_type, $allowed_types)) {
        $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $new_filename = 'img_' . uniqid() . '.' . $file_extension;
        $upload_path = $upload_dir . $new_filename;
        
        if (move_uploaded_file($file['tmp_name'], $upload_path)) {
            echo json_encode(['url' => $upload_path]);
            exit();
        } else {
            echo json_encode(['error' => 'Erro ao mover arquivo']);
            exit();
        }
    } else {
        echo json_encode(['error' => 'Tipo de arquivo não permitido']);
        exit();
    }
} else {
    echo json_encode(['error' => 'Nenhum arquivo enviado ou erro no upload']);
    exit();
}
?>