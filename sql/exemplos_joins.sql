-- =========================================================
-- EXEMPLOS DE JOIN PARA ESTUDAR E APRESENTAR
-- Execute depois de importar speedrun_db.sql.
-- =========================================================

-- 1. INNER JOIN
-- Mostra apenas as runs que possuem usuário, jogo e categoria relacionados.
SELECT u.nickname, j.nome AS jogo, c.nome AS categoria, r.tempo_segundos
FROM runs r
INNER JOIN usuarios u ON u.id_runner = r.id_runner
INNER JOIN jogos j ON j.id_jogo = r.id_jogo
INNER JOIN categorias c ON c.id_categoria = r.id_categoria;

-- 2. LEFT JOIN
-- Mostra todas as categorias, inclusive aquelas que ainda não possuem runs.
SELECT c.nome AS categoria, COUNT(r.id_run) AS quantidade_runs
FROM categorias c
LEFT JOIN runs r ON r.id_categoria = c.id_categoria
GROUP BY c.id_categoria, c.nome;

-- 3. RIGHT JOIN
-- Mostra todas as contas cadastradas, mesmo que ainda não tenham enviado uma run.
SELECT u.nickname, r.id_run, r.tempo_segundos
FROM runs r
RIGHT JOIN usuarios u ON u.id_runner = r.id_runner;

-- 4. CROSS JOIN
-- Gera todas as combinações possíveis entre jogos e categorias.
-- É útil para montar uma lista de desafios disponíveis.
SELECT j.nome AS jogo, c.nome AS categoria
FROM jogos j
CROSS JOIN categorias c;

-- 5. FULL OUTER JOIN (simulado)
-- O MySQL não possui FULL OUTER JOIN nativo.
-- A combinação de LEFT JOIN + RIGHT JOIN com UNION simula esse resultado.
SELECT j.nome AS jogo, r.id_run
FROM jogos j
LEFT JOIN runs r ON r.id_jogo = j.id_jogo

UNION

SELECT j.nome AS jogo, r.id_run
FROM jogos j
RIGHT JOIN runs r ON r.id_jogo = j.id_jogo;

-- 6. MELHOR TEMPO DE CADA RUNNER POR JOGO E CATEGORIA
-- MIN encontra o menor tempo em segundos para cada combinação.
SELECT u.nickname, j.nome AS jogo, c.nome AS categoria,
       MIN(r.tempo_segundos) AS melhor_tempo_em_segundos
FROM runs r
INNER JOIN usuarios u ON u.id_runner = r.id_runner
INNER JOIN jogos j ON j.id_jogo = r.id_jogo
INNER JOIN categorias c ON c.id_categoria = r.id_categoria
GROUP BY u.id_runner, u.nickname, j.id_jogo, j.nome, c.id_categoria, c.nome
ORDER BY j.nome, c.nome, melhor_tempo_em_segundos;
