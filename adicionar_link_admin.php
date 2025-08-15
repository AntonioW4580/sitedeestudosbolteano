<?php
require_once 'includes/config.php';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
            color: #333;
            background-color: #f4f4f4;
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
            background-color: #2c3e50;
            color: white;
            padding: 15px 20px;
            width: 100%;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .header nav {
            display: flex;
            justify-content: space-between;
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
            background-color: #34495e;
        }

        /* Main content area */
        .main-content {
            display: flex;
            flex: 1;
            gap: 20px;
            margin-top: 20px;
        }

        /* Column 1 - Conteúdo Principal */
        .column-main {
            flex: 3;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .posts-list {
            margin-top: 20px;
        }

        .post-item {
            border-bottom: 1px solid #eee;
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
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            height: fit-content;
        }

        .sidebar-title {
            color: #2c3e50;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #3498db;
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
</head>
<body>
    <div class="header">
        <nav>
            <div>
                <a href="index.php">Página Inicial</a>
                <a href="index.php">Página Anterior</a>
                <a href="admin.php">Administração</a>
            </div>
        </nav>
    </div>
    
    <div class="main-content">