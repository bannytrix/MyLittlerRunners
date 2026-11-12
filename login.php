<?php
require_once 'php/conexao.php';
require_once 'php/funcoes.php';

$tituloPagina = "Entrar";
$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identificacao = trim($_POST['identificacao'] ?? '');
    $senha = $_POST['senha'] ?? '';

    // Permite entrar usando o e-mail ou o nickname.
    $sql = "SELECT id_runner, nickname, senha FROM usuarios WHERE email = ? OR nickname = ? LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$identificacao, $identificacao]);
    $runner = $stmt->fetch();

    // password_verify compara a senha digitada com o hash salvo no cadastro.
    if ($runner && password_verify($senha, $runner['senha'])) {
        session_regenerate_id(true);
        $_SESSION['id_runner'] = $runner['id_runner'];
        $_SESSION['nickname'] = $runner['nickname'];

        header("Location: index.php");
        exit();
    } else {
        $erro = "Nickname/e-mail ou senha incorretos.";
    }
}

include 'includes/cabecalho.php';
?>

<main class="container pagina-formulario">
    <section class="painel-formulario">
        <p class="etiqueta">BEM-VINDO DE VOLTA</p>
        <h1>Entrar</h1>
        <p class="texto-secundario">Entre para registrar novas runs e acompanhar seus tempos.</p>

        <?php if ($erro !== ''): ?>
            <div class="alerta erro"><?= e($erro) ?></div>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <div class="campo">
                <label for="identificacao">Nickname ou e-mail</label>
                <input type="text" id="identificacao" name="identificacao" value="<?= e($_POST['identificacao'] ?? '') ?>" required>
            </div>
            <div class="campo">
                <label for="senha">Senha</label>
                <input type="password" id="senha" name="senha" required>
            </div>
            <button class="botao botao-largo" type="submit">Entrar na conta</button>
        </form>

        <p class="texto-centro">Ainda não tem conta? <a href="cadastro.php">Cadastre-se</a></p>
    </section>
</main>

<?php include 'includes/rodape.php'; ?>
