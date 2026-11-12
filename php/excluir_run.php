<?php
require_once __DIR__ . '/conexao.php';

// Somente usuários conectados podem excluir runs.
if (!isset($_SESSION['id_runner'])) {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_run'])) {
    // O WHERE também confere o dono da run: ninguém exclui a run de outra conta.
    $sql = "DELETE FROM runs WHERE id_run = ? AND id_runner = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([(int) $_POST['id_run'], $_SESSION['id_runner']]);
}

header("Location: ../minhas_runs.php");
exit();
?>
