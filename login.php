<?php
session_start(); 
require_once 'includes/config.php';
require_once 'includes/auth.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    if (login($pdo, $username, $password)) {

        header("Location: index.php");
        exit(); // Depois colocar um som tipo hello motto turumturumtrum
    } else {
        $error = "Usuário ou senha inválidos!";
    }
}

require_once 'includes/header.php';
?>

<div class="column-main">
    <div style="max-width: 400px; margin: 50px auto; padding: 20px; background: white; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        <h2 style="text-align: center; margin-bottom: 20px;">Login</h2>
        
        <?php if ($error): ?>
            <div style="background-color: #f8d7da; color: #721c24; padding: 10px; margin-bottom: 15px; border-radius: 4px;">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <form method="post" action="">
            <div style="margin-bottom: 15px;">
                <label for="username" style="display: block; margin-bottom: 5px;">Usuário:</label>
                <input type="text" id="username" name="username" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
            </div>
            
            <div style="margin-bottom: 15px;">
                <label for="password" style="display: block; margin-bottom: 5px;">Senha:</label>
                <input type="password" id="password" name="password" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
            </div>
            
            <button type="submit" style="width: 100%; background-color: #28a745; color: white; padding: 12px; border: none; border-radius: 4px; cursor: pointer;">Entrar</button>
        </form>
        
    
    </div>
</div>

<!-- Coluna Lateral - Removida -->
</div>
</body>
</html>