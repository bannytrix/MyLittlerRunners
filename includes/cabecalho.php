<?php
// Cada página pode definir seu próprio título antes de incluir este arquivo.
if (!isset($tituloPagina)) {
    $tituloPagina = "Speedrun Hub";
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($tituloPagina) ?> | Speedrun Hub</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<header class="topo">
    <div class="topo-conteudo">
        <a class="logo" href="index.php">SPEEDRUN <span>HUB</span></a>

        <nav class="menu">
            <a href="index.php">Início</a>
            <a href="sobre.php">Sobre</a>

            <?php if (isset($_SESSION['id_runner'])): ?>
                <a href="nova_run.php">Adicionar run</a>
                <a href="minhas_runs.php">Minhas runs</a>
                <span class="usuario-menu">Olá, <?= e($_SESSION['nickname']) ?>!</span>
                <a class="link-sair" href="sair.php">Sair</a>
            <?php else: ?>
                <a href="login.php">Entrar</a>
                <a class="link-destaque" href="cadastro.php">Criar conta</a>
            <?php endif; ?>
        </nav>
    </div>
</header>
