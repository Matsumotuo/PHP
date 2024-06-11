<?php
try {
    $conn = new PDO('mysql:host=localhost;dbname=tasks', 'root', '1234');
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro ao se conectar: " . $e->getMessage());
}
?>
