<?php
require_once 'php/conexao.php';
require_once 'php/funcoes.php';
exigirLogin();

$tituloPagina = "Adicionar run";
$erro = '';

// Jogos e categorias vêm do banco. Assim não é preciso digitar os nomes manualmente.
$jogos = $pdo->query("SELECT id_jogo, nome FROM jogos ORDER BY nome")->fetchAll();
$categorias = $pdo->query("SELECT id_categoria, nome FROM categorias ORDER BY nome")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idJogo = (int) ($_POST['id_jogo'] ?? 0);
    $idCategoria = (int) ($_POST['id_categoria'] ?? 0);
    $tempoDigitado = trim($_POST['tempo'] ?? '');
    $versao = trim($_POST['versao'] ?? '');
    $link = trim($_POST['link'] ?? '');

    // Converte HH:MM:SS para segundos, para que o MySQL consiga ordenar os tempos.
    $tempoSegundos = converterTempo($tempoDigitado);

    if ($idJogo <= 0 || $idCategoria <= 0) {
        $erro = "Selecione um jogo e uma categoria.";
    } elseif ($tempoSegundos === false) {
        $erro = "Use o formato HH:MM:SS. Exemplo: 00:12:45.";
    } elseif ($link !== '' && !filter_var($link, FILTER_VALIDATE_URL)) {
        $erro = "O link do vídeo não parece ser uma URL válida.";
    } else {
        // O ID do usuário vem da sessão. Não existe campo para escolher outro runner.
        $sql = "INSERT INTO runs (id_runner, id_jogo, id_categoria, tempo_segundos, versao, link)
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $_SESSION['id_runner'],
            $idJogo,
            $idCategoria,
            $tempoSegundos,
            $versao !== '' ? $versao : null,
            $link !== '' ? $link : null
        ]);

        header("Location: minhas_runs.php?enviada=1");
        exit();
    }
}

include 'includes/cabecalho.php';
?>

<main class="container pagina-formulario">
    <section class="painel-formulario">
        <p class="etiqueta">NOVO PERSONAL BEST</p>
        <h1>Adicionar run</h1>
        <p class="texto-secundario">Run enviada por: <strong><?= e($_SESSION['nickname']) ?></strong>. Seu nickname será associado automaticamente.</p>

        <?php if ($erro !== ''): ?>
            <div class="alerta erro"><?= e($erro) ?></div>
        <?php endif; ?>

        <form method="POST" action="nova_run.php">
            <div class="campo">
                <label for="id_jogo">Jogo</label>
                <select id="id_jogo" name="id_jogo" required>
                    <option value="">Selecione um jogo</option>
                    <?php foreach ($jogos as $jogo): ?>
                        <option value="<?= (int) $jogo['id_jogo'] ?>" <?= (($_POST['id_jogo'] ?? '') == $jogo['id_jogo']) ? 'selected' : '' ?>>
                            <?= e($jogo['nome']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="campo">
                <label for="id_categoria">Categoria</label>
                <select id="id_categoria" name="id_categoria" required>
                    <option value="">Selecione uma categoria</option>
                    <?php foreach ($categorias as $categoria): ?>
                        <option value="<?= (int) $categoria['id_categoria'] ?>" <?= (($_POST['id_categoria'] ?? '') == $categoria['id_categoria']) ? 'selected' : '' ?>>
                            <?= e($categoria['nome']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="campo">
                <label for="tempo">Tempo final (HH:MM:SS)</label>
                <input type="text" id="tempo" name="tempo" placeholder="00:12:45" pattern="[0-9]{1,3}:[0-5][0-9]:[0-5][0-9]" value="<?= e($_POST['tempo'] ?? '') ?>" required>
                <small>Exemplo: 00:12:45 representa 12 minutos e 45 segundos.</small>
            </div>

            <div class="campo">
                <label for="versao">Versão / plataforma do jogo (opcional)</label>
                <input type="text" id="versao" name="versao" maxlength="40" placeholder="Ex.: 1.0 / versão Steam" value="<?= e($_POST['versao'] ?? '') ?>">
            </div>

            <div class="campo">
                <label for="link">Link do vídeo da run (opcional)</label>
                <input type="url" id="link" name="link" maxlength="500" placeholder="https://..." value="<?= e($_POST['link'] ?? '') ?>">
            </div>

            <button class="botao botao-largo" type="submit">Salvar run</button>
        </form>
    </section>
</main>

<?php include 'includes/rodape.php'; ?>
