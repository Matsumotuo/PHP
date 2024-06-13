<?php
require __DIR__ . '/connect.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if ($conn) {
        try {
            $stmt = $conn->prepare("SELECT * FROM users WHERE username = :username");
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                header('Location: index.php'); // Alterado para redirecionar para a página index.php
                exit();
            } else {
                $_SESSION['error'] = "Nome de usuário ou senha incorretos.";
            }
        } catch (PDOException $e) {
            $_SESSION['error'] = "Erro ao executar a consulta: " . $e->getMessage();
        }
    } else {
        $_SESSION['error'] = "Erro ao se conectar ao banco de dados.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Login</title>
</head>
<body>
    <div class="container">
        <h1>Login</h1>
        <?php if (isset($_SESSION['error'])) : ?>
            <div class="alert-error"><?php echo $_SESSION['error']; ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
        <form action="" method="POST">
            <label for="username">Nome de Usuário:</label>
            <input type="text" name="username">
            <label for="password">Senha:</label>
            <input type="password" name="password">
            <button type="submit">Entrar</button>
        </form>
        <a href="cadastro.php">Cadastrar</a>
        <a href="lembrarsenha.php">Lembrar Senha</a>
    </div>
</body>
</html>
