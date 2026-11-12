<?php
require_once 'php/conexao.php';
require_once 'php/funcoes.php';

$tituloPagina = "Início";

// Busca as categorias e conta quantas runs existem em cada uma.
// LEFT JOIN mantém a categoria na tela mesmo quando ela ainda não tem runs.
$sqlCategorias = "
    SELECT c.id_categoria, c.nome, c.descricao, COUNT(r.id_run) AS total_runs
    FROM categorias c
    LEFT JOIN runs r ON r.id_categoria = c.id_categoria
    GROUP BY c.id_categoria, c.nome, c.descricao
    ORDER BY c.nome
";
$categorias = $pdo->query($sqlCategorias)->fetchAll();

// Busca os jogos e suas plataformas para preencher o filtro.
$sqlJogos = "
    SELECT j.id_jogo, j.nome, p.nome AS plataforma
    FROM jogos j
    INNER JOIN plataformas p ON p.id_plataforma = j.id_plataforma
    ORDER BY j.nome
";
$jogos = $pdo->query($sqlJogos)->fetchAll();

// Lê os filtros escolhidos na página.
$filtroJogo = isset($_GET['jogo']) ? (int) $_GET['jogo'] : 0;
$filtroCategoria = isset($_GET['categoria']) ? (int) $_GET['categoria'] : 0;

// Esta consulta mostra somente a melhor run de cada pessoa em cada jogo/categoria.
// Os INNER JOIN conectam runs, usuários, jogos, plataformas e categorias.
$sqlRuns = "
    SELECT
        r.id_run,
        r.tempo_segundos,
        r.versao,
        r.link,
        r.data_registro,
        u.nickname,
        j.id_jogo,
        j.nome AS jogo,
        p.nome AS plataforma,
        c.id_categoria,
        c.nome AS categoria
    FROM runs r
    INNER JOIN usuarios u ON u.id_runner = r.id_runner
    INNER JOIN jogos j ON j.id_jogo = r.id_jogo
    INNER JOIN plataformas p ON p.id_plataforma = j.id_plataforma
    INNER JOIN categorias c ON c.id_categoria = r.id_categoria
    WHERE r.id_run = (
        SELECT r2.id_run
        FROM runs r2
        WHERE r2.id_runner = r.id_runner
          AND r2.id_jogo = r.id_jogo
          AND r2.id_categoria = r.id_categoria
        ORDER BY r2.tempo_segundos ASC, r2.id_run ASC
        LIMIT 1
    )
";

// Os filtros são adicionados somente quando o visitante escolhe uma opção.
$parametros = [];
if ($filtroJogo > 0) {
    $sqlRuns .= " AND r.id_jogo = :jogo";
    $parametros[':jogo'] = $filtroJogo;
}
if ($filtroCategoria > 0) {
    $sqlRuns .= " AND r.id_categoria = :categoria";
    $parametros[':categoria'] = $filtroCategoria;
}

// Agrupa por jogo e categoria; dentro de cada grupo, o menor tempo vem primeiro.
$sqlRuns .= " ORDER BY j.nome, c.nome, r.tempo_segundos, u.nickname";
$stmtRuns = $pdo->prepare($sqlRuns);
$stmtRuns->execute($parametros);
$runs = $stmtRuns->fetchAll();

include 'includes/cabecalho.php';
?>

<main class="container">
    <section class="hero">
        <div>
            <p class="etiqueta">REGISTRE. COMPARE. SUPERE.</p>
            <h1>Seu melhor tempo<br><span>merece um lugar no ranking.</span></h1>
            <p class="hero-texto">Acompanhe speedruns de jogos diferentes, registre seus recordes pessoais e veja como você está na comunidade.</p>
            <?php if (isset($_SESSION['id_runner'])): ?>
                <a class="botao" href="nova_run.php">+ Adicionar minha run</a>
            <?php else: ?>
                <a class="botao" href="cadastro.php">Criar minha conta</a>
            <?php endif; ?>
        </div>
        <div class="hero-selo">
            <span>PB</span>
            <small>PERSONAL BEST</small>
        </div>
    </section>

    <section class="secao">
        <div class="titulo-secao">
            <div>
                <p class="etiqueta">ESCOLHA O DESAFIO</p>
                <h2>Categorias de speedrun</h2>
            </div>
        </div>

        <div class="grade-categorias">
            <?php foreach ($categorias as $categoria): ?>
                <a class="cartao-categoria" href="index.php?categoria=<?= (int) $categoria['id_categoria'] ?>">
                    <h3><?= e($categoria['nome']) ?></h3>
                    <p><?= e($categoria['descricao']) ?></p>
                    <small><?= (int) $categoria['total_runs'] ?> run(s) registrada(s)</small>
                </a>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="secao">
        <div class="titulo-secao">
            <div>
                <p class="etiqueta">TEMPO É TUDO</p>
                <h2>Ranking de melhores runs</h2>
                <p class="texto-secundario">Cada jogador aparece com seu melhor tempo em cada jogo e categoria.</p>
            </div>
        </div>

        <form class="filtros" method="GET" action="index.php">
            <div class="campo-filtro">
                <label for="jogo">Jogo</label>
                <select name="jogo" id="jogo">
                    <option value="0">Todos os jogos</option>
                    <?php foreach ($jogos as $jogo): ?>
                        <option value="<?= (int) $jogo['id_jogo'] ?>" <?= $filtroJogo === (int) $jogo['id_jogo'] ? 'selected' : '' ?>>
                            <?= e($jogo['nome']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="campo-filtro">
                <label for="categoria">Categoria</label>
                <select name="categoria" id="categoria">
                    <option value="0">Todas as categorias</option>
                    <?php foreach ($categorias as $categoria): ?>
                        <option value="<?= (int) $categoria['id_categoria'] ?>" <?= $filtroCategoria === (int) $categoria['id_categoria'] ? 'selected' : '' ?>>
                            <?= e($categoria['nome']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button class="botao botao-filtro" type="submit">Filtrar</button>
            <a class="botao-secundario" href="index.php">Limpar</a>
        </form>

        <?php if (count($runs) > 0): ?>
            <div class="tabela-responsiva">
                <table>
                    <thead>
                        <tr>
                            <th>Pos.</th>
                            <th>Runner</th>
                            <th>Jogo</th>
                            <th>Categoria</th>
                            <th>Plataforma</th>
                            <th>Melhor tempo</th>
                            <th>Versão</th>
                            <th>Vídeo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // A posição volta para 1 quando muda o jogo ou a categoria.
                        $grupoAnterior = '';
                        $posicao = 0;
                        foreach ($runs as $run):
                            $grupoAtual = $run['id_jogo'] . '-' . $run['id_categoria'];
                            if ($grupoAtual !== $grupoAnterior) {
                                $posicao = 1;
                                $grupoAnterior = $grupoAtual;
                            } else {
                                $posicao++;
                            }
                        ?>
                            <tr>
                                <td><span class="posicao"><?= $posicao ?>º</span></td>
                                <td class="nick"><?= e($run['nickname']) ?></td>
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
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="mensagem-vazia">
                <h3>Nenhuma run encontrada ainda.</h3>
                <p>Escolha outros filtros ou seja a primeira pessoa a registrar um tempo!</p>
                <?php if (isset($_SESSION['id_runner'])): ?>
                    <a class="botao" href="nova_run.php">Adicionar uma run</a>
                <?php else: ?>
                    <a class="botao" href="cadastro.php">Criar conta</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </section>
</main>

<?php include 'includes/rodape.php'; ?>
