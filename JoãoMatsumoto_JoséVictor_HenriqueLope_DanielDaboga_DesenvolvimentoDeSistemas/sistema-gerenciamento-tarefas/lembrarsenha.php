<?php
require __DIR__ . '/connect.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];

    if ($conn) {
        try {
            // Verificar se o e-mail existe no banco de dados
            $stmt_check_email = $conn->prepare("SELECT * FROM users WHERE email = :email");
            $stmt_check_email->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt_check_email->execute();
            
            if ($stmt_check_email->rowCount() > 0) {
                // E-mail existe no banco de dados, você pode enviar instruções para redefinir a senha
                // Por enquanto, vamos apenas simular que o e-mail foi enviado com sucesso

                // Redirecionar para a página de login
                $_SESSION['success'] = "Instruções de redefinição de senha foram enviadas para o seu e-mail.";
                header('Location: login.php');
                exit();
            } else {
                $_SESSION['error'] = "E-mail não cadastrado.";
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
    <title>Lembrar Senha</title>
</head>
<body>
    <div class="container">
        <h1>Lembrar Senha</h1>
        <?php if (isset($_SESSION['error'])) : ?>
            <div class="alert-error"><?php echo $_SESSION['error']; ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
        <?php if (isset($_SESSION['success'])) : ?>
            <div class="alert-success"><?php echo $_SESSION['success']; ?></div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
        <p>Insira o seu e-mail abaixo para redefinir a sua senha.</p>
        <form action="" method="POST">
            <label for="email">E-mail:</label>
            <input type="email" name="email" required>
            <button type="submit">Enviar Instruçõe
