<?php
require __DIR__ . '/connect.php';
session_start();

// Verifique se o parâmetro ID da tarefa foi passado na URL
if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit();
}

$taskId = $_GET['id'];

if ($conn) {
    try {
        // Prepare a consulta para obter os detalhes da tarefa com base no ID
        $stmt = $conn->prepare("SELECT * FROM tasks WHERE id = :taskId");
        $stmt->bindParam(':taskId', $taskId, PDO::PARAM_INT);
        $stmt->execute();
        $task = $stmt->fetch(PDO::FETCH_ASSOC);

        // Se não houver nenhuma tarefa com o ID fornecido, redirecione de volta para a página inicial
        if (!$task) {
            header('Location: index.php');
            exit();
        }
    } catch (PDOException $e) {
        die("Erro ao buscar detalhes da tarefa: " . $e->getMessage());
    }
} else {
    echo "Erro ao se conectar ao banco de dados.";
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styledetails.css">
    <link href="https://fonts.googleapis.com/css2?family=Ubuntu:ital,wght@0,300;0,400;0,500;0,700&display=swap" rel="stylesheet">
    <title>Detalhes da Tarefa</title>
</head>
<body>
    <div class="container">
        <h1>Detalhes da Tarefa</h1>
        <ul>
            <li><strong>Nome da Tarefa:</strong> <?php echo htmlspecialchars($task['task_name']); ?></li>
            <li><strong>Descrição:</strong> <?php echo htmlspecialchars($task['task_description']); ?></li>
            <li><strong>Data:</strong> <?php echo htmlspecialchars($task['task_date']); ?></li>
            <?php if (!empty($task['task_image'])) : ?>
                <li><strong>Imagem:</strong> <img src="uploads/<?php echo $task['task_image']; ?>" alt="Imagem da Tarefa"></li>
            <?php endif; ?>
        </ul>
        <a href="index.php" class="back-link">Voltar para a Lista de Tarefas</a>
    </div>
</body>
</html>

