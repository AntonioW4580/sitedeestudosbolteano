<?php
require_once 'includes/config.php';

$sql_check = "SELECT COUNT(*) FROM users WHERE username = :username";
$stmt_check = $pdo->prepare($sql_check);
$stmt_check->bindValue(':username', 'admin', PDO::PARAM_STR);
$stmt_check->execute();
$user_exists = $stmt_check->fetchColumn();

if ($user_exists > 0) {
    echo "Usuário admin já existe!<br>";
    echo "Login: admin<br>";
    echo "Senha: admin123<br>";
} else {

    $username = 'admin';
    $password = 'admin123'; // Senha de teste
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (username, password, email, is_admin) VALUES (:username, :password, :email, :is_admin)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':username', $username, PDO::PARAM_STR);
    $stmt->bindValue(':password', $hashed_password, PDO::PARAM_STR);
    $stmt->bindValue(':email', 'admin@example.com', PDO::PARAM_STR);
    $stmt->bindValue(':is_admin', 1, PDO::PARAM_INT);

    try {
        $stmt->execute();
        echo "Usuário admin criado com sucesso!<br>";
        echo "Login: admin<br>";
        echo "Senha: admin123<br>";
    } catch (Exception $e) {
        echo "Erro: " . $e->getMessage();
    }
}

echo "<br><a href='login.php'>Ir para Login</a>";
?>