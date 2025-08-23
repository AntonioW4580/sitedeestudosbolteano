<?php
require_once 'includes/config.php';

$username = 'admin';
$new_password = 'admin123';
$hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

$sql = "UPDATE users SET password = :password WHERE username = :username";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':password', $hashed_password, PDO::PARAM_STR);
$stmt->bindValue(':username', $username, PDO::PARAM_STR);

try {
    $stmt->execute();
    if ($stmt->rowCount() > 0) {
        echo "Senha do usuário admin resetada com sucesso!<br>";
        echo "Login: admin<br>";
        echo "Senha: admin123<br>";
    } else {
        echo "Usuário admin não encontrado!<br>";
    }
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage();
}

echo "<br><a href='login.php'>Ir para Login</a>";
?>