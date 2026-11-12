<?php
require_once 'php/conexao.php';
require_once 'php/funcoes.php';

$tituloPagina = "Criar conta";
$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nickname = trim($_POST['nickname'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';
    $confirmarSenha = $_POST['confirmar_senha'] ?? '';

    if ($nickname === '' || $email === '' || $senha === '') {
        $erro = "Preencha todos os campos.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = "Digite um e-mail válido.";
    } elseif (strlen($senha) < 6) {
        $erro = "A senha precisa ter pelo menos 6 caracteres.";
    } elseif ($senha !== $confirmarSenha) {
        $erro = "As senhas não são iguais.";
    } else {
        try {
            // A senha nunca é salva em texto puro: password_hash cria um hash seguro.
            $senhaProtegida = password_hash($senha, PASSWORD_DEFAULT);

            $sql = "INSERT INTO usuarios (nickname, email, senha) VALUES (?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$nickname, $email, $senhaProtegida]);

            // Depois do cadastro, a pessoa já entra na própria conta.
            session_regenerate_id(true);
            $_SESSION['id_runner'] = $pdo->lastInsertId();
            $_SESSION['nickname'] = $nickname;

            header("Location: index.php");
            exit();
        } catch (PDOException $erroBanco) {
            // O banco não permite repetir nickname ou e-mail.
            $erro = "Esse nickname ou e-mail já está cadastrado.";
        }
    }
}

include 'includes/cabecalho.php';
?>

<main class="container pagina-formulario">
    <section class="painel-formulario">
        <p class="etiqueta">FAÇA PARTE DA COMUNIDADE</p>
        <h1>Criar conta</h1>
        <p class="texto-secundario">Sua conta será usada para identificar automaticamente as runs que você enviar.</p>

        <?php if ($erro !== ''): ?>
            <div class="alerta erro"><?= e($erro) ?></div>
        <?php endif; ?>

        <form method="POST" action="cadastro.php">
            <div class="campo">
                <label for="nickname">Nickname</label>
                <input type="text" id="nickname" name="nickname" maxlength="40" value="<?= e($_POST['nickname'] ?? '') ?>" required>
            </div>
            <div class="campo">
                <label for="email">E-mail</label>
                <input type="email" id="email" name="email" maxlength="120" value="<?= e($_POST['email'] ?? '') ?>" required>
            </div>
            <div class="campo">
                <label for="senha">Senha</label>
                <input type="password" id="senha" name="senha" minlength="6" required>
            </div>
            <div class="campo">
                <label for="confirmar_senha">Confirme a senha</label>
                <input type="password" id="confirmar_senha" name="confirmar_senha" minlength="6" required>
            </div>
            <button class="botao botao-largo" type="submit">Criar minha conta</button>
        </form>

        <p class="texto-centro">Já tem uma conta? <a href="login.php">Entrar</a></p>
    </section>
</main>

<?php include 'includes/rodape.php'; ?>
