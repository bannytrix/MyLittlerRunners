<?php
require_once 'php/conexao.php';
require_once 'php/funcoes.php';
$tituloPagina = "Sobre";
include 'includes/cabecalho.php';
?>

<main class="container pagina-sobre">
    <section class="hero hero-menor">
        <div>
            <p class="etiqueta">SOBRE O PROJETO</p>
            <h1>O que é <span>Speedrun Hub?</span></h1>
            <p class="hero-texto">Speedrun é terminar um jogo no menor tempo possível, seguindo as regras de uma categoria. O Speedrun Hub permite que cada pessoa registre seus tempos e compare seus melhores resultados.</p>
        </div>
    </section>

    <section class="secao texto-sobre">
        <h2>Como funciona?</h2>
        <p>Crie uma conta, entre no sistema e use o botão <strong>Adicionar run</strong>. O site identifica seu nickname pela conta conectada, então não é necessário digitar seu nome toda vez.</p>
        <p>O tempo é guardado no banco em segundos. Assim, o MySQL consegue comparar os resultados numericamente e organizar o ranking do menor para o maior tempo.</p>
        <h2>Jogos e categorias</h2>
        <p>Os jogos disponíveis incluem Super Mario 64, Dark Deception, Minecraft, Devil May Cry 3 e Cuphead. Já as categorias, como Any%, 100% e Glitchless, definem as regras usadas para completar cada jogo.</p>
    </section>
</main>

<?php include 'includes/rodape.php'; ?>
