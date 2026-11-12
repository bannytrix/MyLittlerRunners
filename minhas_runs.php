<?php
require_once 'php/conexao.php';
require_once 'php/funcoes.php';
exigirLogin();

$tituloPagina = "Minhas runs";

// INNER JOIN busca os nomes do jogo e da categoria ligados a cada run do usuário.
$sql = "
    SELECT r.id_run, r.tempo_segundos, r.versao, r.link, r.data_registro,
           j.nome AS jogo, c.nome AS categoria, p.nome AS plataforma
    FROM runs r
    INNER JOIN jogos j ON j.id_jogo = r.id_jogo
    INNER JOIN categorias c ON c.id_categoria = r.id_categoria
    INNER JOIN plataformas p ON p.id_plataforma = j.id_plataforma
    WHERE r.id_runner = ?
    ORDER BY r.data_registro DESC
";
$stmt = $pdo->prepare($sql);
$stmt->execute([$_SESSION['id_runner']]);
$minhasRuns = $stmt->fetchAll();

include 'includes/cabecalho.php';
?>

<main class="container secao">
    <div class="titulo-secao titulo-com-botao">
        <div>
            <p class="etiqueta">SEU HISTÓRICO</p>
            <h1>Minhas runs</h1>
            <p class="texto-secundario">Aqui aparecem os tempos enviados pela sua conta.</p>
        </div>
        <a class="botao" href="nova_run.php">+ Adicionar run</a>
    </div>

    <?php if (isset($_GET['enviada'])): ?>
        <div class="alerta sucesso">Run registrada com sucesso!</div>
    <?php endif; ?>

    <?php if (count($minhasRuns) > 0): ?>
        <div class="tabela-responsiva">
            <table>
                <thead>
                    <tr>
                        <th>Jogo</th>
                        <th>Categoria</th>
                        <th>Plataforma</th>
                        <th>Tempo</th>
                        <th>Versão</th>
                        <th>Vídeo</th>
                        <th>Ação</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($minhasRuns as $run): ?>
                        <tr>
                            <td><?= e($run['jogo']) ?></td>
                            <td><?= e($run['categoria']) ?></td>
                            <td><?= e($run['plataforma']) ?></td>
                            <td class="tempo"><?= formatarTempo((int) $run['tempo_segundos']) ?></td>
                            <td><?= e($run['versao'] ?: '—') ?></td>
                            <td>
                                <?php if (!empty($run['link'])): ?>
                                    <a class="link-video" href="<?= e($run['link']) ?>" target="_blank" rel="noopener noreferrer">Assistir</a>
                                <?php else: ?>
                                    —
                                <?php endif; ?>
                            </td>
                            <td>
                                <form class="form-excluir" method="POST" action="php/excluir_run.php" data-confirmar="Tem certeza que deseja excluir esta run?">
                                    <input type="hidden" name="id_run" value="<?= (int) $run['id_run'] ?>">
                                    <button class="botao-excluir" type="submit">Excluir</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="mensagem-vazia">
            <h3>Você ainda não enviou nenhuma run.</h3>
            <p>Quando registrar seu primeiro tempo, ele aparecerá aqui.</p>
            <a class="botao" href="nova_run.php">Adicionar minha primeira run</a>
        </div>
    <?php endif; ?>
</main>

<?php include 'includes/rodape.php'; ?>
