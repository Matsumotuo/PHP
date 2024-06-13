<?php
require __DIR__ . '/connect.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $_SESSION['error'] = "Por favor, preencha todos os campos.";
    } else {
        if ($password !== $confirm_password) {
            $_SESSION['error'] = "As senhas não coincidem.";
        } else {
            if ($conn) {
                try {
                    // Verificar se o e-mail já está em uso
                    $stmt_check_email = $conn->prepare("SELECT * FROM users WHERE email = :email");
                    $stmt_check_email->bindParam(':email', $email, PDO::PARAM_STR);
                    $stmt_check_email->execute();
                    if ($stmt_check_email->rowCount() > 0) {
                        $_SESSION['error'] = "Este e-mail já está em uso.";
                    } else {
                        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
                        $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (:username, :email, :password)");
                        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
                        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
                        $stmt->bindParam(':password', $hashed_password, PDO::PARAM_STR);

                        if ($stmt->execute()) {
                            $_SESSION['success'] = "Cadastro realizado com sucesso. Faça login.";
                            header('Location: login.php'); // Alterado para redirecionar para a página de login
                            exit();
                        } else {
                            $_SESSION['error'] = "Erro ao cadastrar usuário.";
                        }
                    }
                } catch (PDOException $e) {
                    $_SESSION['error'] = "Erro ao executar a consulta: " . $e->getMessage();
                }
            } else {
                $_SESSION['error'] = "Erro ao se conectar ao banco de dados.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Cadastro</title>
</head>
<body>
    <div class="container">
        <h1>Cadastro</h1>
        <?php if (isset($_SESSION['error'])) : ?>
            <div class="alert-error"><?php echo $_SESSION['error']; ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
        <form action="" method="POST">
            <label for="username">Nome de Usuário:</label>
            <input type="text" name="username">
            <label for="email">E-mail:</label>
            <input type="email" name="email">
            <label for="password">Senha:</label>
            <input type="password" name="password">
            <label for="confirm_password">Confirme a Senha:</label>
            <input type="password" name="confirm_password">
            <button type="submit">Cadastrar</button>
        </form>
        <a href="login.php">Voltar para Login</a>
    </div>
</body>
</html>
