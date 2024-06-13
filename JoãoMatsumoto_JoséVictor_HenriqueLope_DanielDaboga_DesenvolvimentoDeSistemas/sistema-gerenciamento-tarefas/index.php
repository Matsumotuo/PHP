<?php
require __DIR__ . '/connect.php';
session_start();

if (!isset($_SESSION['tasks'])) {
    $_SESSION['tasks'] = array();
}

if ($conn) {
    // Fetch tasks for the current user
    try {
        $user_id = $_SESSION['user_id']; // Obtém o ID do usuário atualmente logado
        $stmt_fetch = $conn->prepare("SELECT * FROM tasks WHERE user_id = :user_id");
        $stmt_fetch->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt_fetch->execute();
        $tasks = $stmt_fetch->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Erro ao buscar tarefas: " . $e->getMessage());
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Handle form submission for adding tasks
        if (isset($_POST['task_name']) && !empty($_POST['task_name'])) {
            $task_name = $_POST['task_name'];
            $task_description = $_POST['task_description'] ?? '';
            $task_date = $_POST['task_date'] ?? date('Y-m-d');
            $file_name = '';

            // Insert task into database
            try {
                $stmt = $conn->prepare('INSERT INTO tasks (task_name, task_description, task_date, user_id) 
                                        VALUES (:name, :description, :date, :user_id)');
                $stmt->bindParam(':name', $task_name, PDO::PARAM_STR);
                $stmt->bindParam(':description', $task_description, PDO::PARAM_STR);
                $stmt->bindParam(':date', $task_date, PDO::PARAM_STR);
                $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

                if ($stmt->execute()) {
                    $_SESSION['success'] = "Tarefa cadastrada com sucesso.";
                } else {
                    $_SESSION['error'] = "Erro ao cadastrar a tarefa.";
                }
            } catch (PDOException $e) {
                $_SESSION['error'] = "Erro ao executar a consulta: " . $e->getMessage();
            }

            header('Location: ' . $_SERVER['PHP_SELF']);
            exit();
        }
    }

    // Limpar tarefas
    if (isset($_GET['clear'])) {
        try {
            $stmt_clear = $conn->prepare("DELETE FROM tasks WHERE user_id = :user_id");
            $stmt_clear->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            if ($stmt_clear->execute()) {
                $_SESSION['success'] = "Tarefas limpas com sucesso.";
            } else {
                $_SESSION['error'] = "Erro ao limpar as tarefas.";
            }
        } catch (PDOException $e) {
            $_SESSION['error'] = "Erro ao limpar as tarefas: " . $e->getMessage();
        }
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit();
    }
} else {
    echo "Erro ao se conectar ao banco de dados.";
}

if (isset($_POST['logout'])) {
    session_destroy();
    header('Location: home.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Ubuntu:ital,wght@0,300;0,400;0,500;0,700&display=swap" rel="stylesheet">
    <title>Gerenciador de Tarefas</title>
</head>
<body>
    <div class="container">
        <?php
        if (isset($_SESSION['success'])) {
        ?>
            <div class="alert-success"><?php echo $_SESSION['success']; ?></div>
        <?php
            unset($_SESSION['success']);
        }
        ?>

        <?php
        if (isset($_SESSION['error'])) {
        ?>
            <div class="alert-error"><?php echo $_SESSION['error']; ?></div>
        <?php
            unset($_SESSION['error']);
        }
        ?>
        <div class="header">
            <h1>Gerenciador de Tarefas</h1>
        </div>

        <div class="form">
            <form action="" method="POST" enctype="multipart/form-data">
                <label for="task_name">Tarefa: </label>
                <input type="text" name="task_name" placeholder="Nome da Tarefa" required>
                <label for="task_description">Descrição: </label>
                <input type="text" name="task_description" placeholder="Descrição da Tarefa">
                <label for="task_date">Data: </label>
                <input type="date" name="task_date" value="<?php echo date('Y-m-d'); ?>">
                <button type="submit">Cadastrar</button>
            </form>
        </div>

        <div class="separator"></div>

        <div class="list-tasks">
            <?php if (!empty($tasks)) : ?>
                <ul>
                    <?php foreach ($tasks as $task) : ?>
                        <li>
                            <a href='details.php?id=<?php echo $task['id']; ?>'><?php echo htmlspecialchars($task['task_name']); ?></a>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <form action="?clear" method="POST">
                    <button type="submit">Limpar Tarefas</button>
                </form>
            <?php else : ?>
                <p>Nenhuma tarefa encontrada.</p>
            <?php endif; ?>
        </div>

        <a href="home.php">Sair</a>

    </div>
</body>
</html>