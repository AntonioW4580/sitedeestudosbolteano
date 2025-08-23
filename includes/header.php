<?php
require_once 'config.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$remote_ip = $_SERVER['REMOTE_ADDR'];
$allowed_ips = ['127.0.0.1', '::1', '203.111.222.333']; 

// IP PRA QUEM VAI ACESSAR: $allowed_ips = ['127.0.0.1', '::1', '203.111.222.333'];
// Caso queria testar deixe o allowed_ips = []
$is_your_ip = in_array($remote_ip, $allowed_ips);


?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Meu Site</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #050000ff;
            background-color: rgba(255, 255, 255, 1);
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            display: flex;
            min-height: 100vh;
        }

        /* Header */
        .header {
            background-color: rgba(93, 151, 0, 1);
            color: white;
            padding: 15px 20px;
            width: 100%;
            position: sticky;
            top: 0;
            z-index: 100;
            border-bottom: 1px solid #99ff00ff;
        }

        .header nav {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .header nav a {
            color: white;
            text-decoration: none;
            padding: 8px 12px;
            border-radius: 4px;
            transition: background-color 0.3s;
        }

        .header nav a:hover {
            background-color: #0375f8ff;
        }

        .header nav div {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 15px; /* ESPAÇAMENTO ENTRE BOTÕES */
        }

        /* Main content area */
        .main-content {
            display: flex;
            flex: 2;
            gap: 20px;
            margin-top: 20px;
        }

        /* Column 1 - Conteúdo Principal */
        .column-main {
            flex: 4;
            background-color: #ffffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 3px 5px 5px rgba(0,0,0,0.1);
            margin-left: -60px; /* Move para ESQUERDA */

        }

        .posts-list {
            margin-top: 20px;
        }

        .post-item {
            border-bottom: 1px solid #293a42;
            padding: 20px 0;
        }

        .post-item:last-child {
            border-bottom: none;
        }

        .post-title {
            color: #2c3e50;
            margin-bottom: 10px;
            font-size: 1.3em;
        }
        
        .post-date {
            color: #7f8c8d;
            font-size: 0.9em;
            margin-bottom: 10px;
        }

        .post-description {
            color: #555;
            margin-bottom: 15px;
        }

        .post-text {
            color: #666;
            line-height: 1.6;
        }

        /* Column 2 - Menu Lateral */
        .column-sidebar {
            flex: 1;
            background-color: rgba(255, 255, 255, 1);
            padding: 20px;
            border-radius: 8px;
            box-shadow: 2px 5px 5px rgba(255, 0, 0, 0.1);
            height: fit-content;
            margin-right: -70px;
        }

        .sidebar-title {
            color: #000000ff;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #000000ff;
        }

        .sidebar-links {
            list-style: none;
        }

        .sidebar-links li {
            margin-bottom: 10px;
        }

        .sidebar-links a {
            color: #3498db;
            text-decoration: none;
            transition: color 0.3s;
        }

        .sidebar-links a:hover {
            color: #2980b9;
            text-decoration: underline;
        }

        /* Pagination */
        .pagination {
            margin-top: 30px;
            text-align: center;
        }

        .pagination a {
            display: inline-block;
            padding: 8px 16px;
            margin: 0 5px;
            background-color: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }

        .pagination a:hover {
            background-color: #2980b9;
        }

        .pagination .current {
            background-color: #2c3e50;
            cursor: default;
        }

        .pagination .current:hover {
            background-color: #2c3e50;
        }

        /* Single Post View */
        .single-post {
            margin-top: 20px;
        }

        .single-post h1 {
            color: #2c3e50;
            margin-bottom: 15px;
        }

        .single-post .post-meta {
            color: #7f8c8d;
            margin-bottom: 20px;
            font-size: 0.9em;
        }

        .single-post .post-content {
            line-height: 1.8;
            color: #555;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .container {
                flex-direction: column;
            }
            
            .main-content {
                flex-direction: column;
            }
        }

        

    </style>

    <style>
    .post-content,
    .post-content * {
        color: #000000ff !important;
        font-family: 'Crimson Text', Georgia, serif !important;
    }
    
    .post-content img {
        max-width: 870px !important;
        max-height: 382px !important;
        object-fit: contain !important;
        display: inline-block !important;
    }
</style>


</head>
<body>
    <div class="header">
        <nav>
            <div>
                <a href="index.php">🏚️ Página Inicial</a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1): ?>
                        <a href="admin.php">🤖Administração</a>
                    <?php endif; ?>
                    <a href="logout.php">🚪 Sair (<?php echo htmlspecialchars($_SESSION['username']); ?>)</a>
                <?php elseif ($is_your_ip): ?>
                    <!-- Apenas para seu IP: mostrar botão de login -->
                    <a href="login.php">🤖 Login</a>
                <?php endif; ?>
                <a href="pomodoro.php">🍅 Pomodoro</a>
                <a href="questoes.php">🎯 Lista de Questões</a>

            </div>
        </nav>
    </div>
    
    <div class="container">
        <div class="main-content">