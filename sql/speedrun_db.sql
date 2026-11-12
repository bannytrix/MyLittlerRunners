-- =========================================================
-- SPEEDRUN HUB - BANCO DE DADOS MYSQL
-- Importe este arquivo pelo phpMyAdmin ou pelo MySQL.
-- =========================================================

CREATE DATABASE IF NOT EXISTS speedrun_db
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE speedrun_db;

-- 1. Plataformas: cada jogo cadastrado aponta para uma plataforma.
CREATE TABLE IF NOT EXISTS plataformas (
    id_plataforma INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(80) NOT NULL UNIQUE
) ENGINE=InnoDB;

-- 2. Usuários/runners: cada conta possui nickname, e-mail e senha protegida.
CREATE TABLE IF NOT EXISTS usuarios (
    id_runner INT AUTO_INCREMENT PRIMARY KEY,
    nickname VARCHAR(40) NOT NULL UNIQUE,
    email VARCHAR(120) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 3. Jogos: o nome do jogo e sua plataforma ficam cadastrados uma única vez.
CREATE TABLE IF NOT EXISTS jogos (
    id_jogo INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL UNIQUE,
    id_plataforma INT NOT NULL,
    CONSTRAINT fk_jogos_plataformas
        FOREIGN KEY (id_plataforma)
        REFERENCES plataformas(id_plataforma)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB;

-- 4. Categorias: regras de speedrun, como Any%, 100% e Glitchless.
CREATE TABLE IF NOT EXISTS categorias (
    id_categoria INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(50) NOT NULL UNIQUE,
    descricao VARCHAR(255) NOT NULL
) ENGINE=InnoDB;

-- 5. Runs: cada registro pertence a um usuário, jogo e categoria.
-- O tempo é salvo em segundos para o MySQL ordenar corretamente.
CREATE TABLE IF NOT EXISTS runs (
    id_run INT AUTO_INCREMENT PRIMARY KEY,
    id_runner INT NOT NULL,
    id_jogo INT NOT NULL,
    id_categoria INT NOT NULL,
    tempo_segundos INT UNSIGNED NOT NULL,
    versao VARCHAR(40) NULL,
    link VARCHAR(500) NULL,
    data_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_runs_runner_jogo_categoria (id_runner, id_jogo, id_categoria),
    INDEX idx_runs_jogo_categoria_tempo (id_jogo, id_categoria, tempo_segundos),

    CONSTRAINT fk_runs_usuarios
        FOREIGN KEY (id_runner)
        REFERENCES usuarios(id_runner)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    CONSTRAINT fk_runs_jogos
        FOREIGN KEY (id_jogo)
        REFERENCES jogos(id_jogo)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    CONSTRAINT fk_runs_categorias
        FOREIGN KEY (id_categoria)
        REFERENCES categorias(id_categoria)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB;

-- =========================================================
-- DADOS INICIAIS: plataformas
-- =========================================================
INSERT IGNORE INTO plataformas (nome) VALUES
('Nintendo 64'),
('PC'),
('PlayStation 2');

-- =========================================================
-- DADOS INICIAIS: jogos
-- =========================================================
INSERT IGNORE INTO jogos (nome, id_plataforma)
SELECT 'Super Mario 64', id_plataforma FROM plataformas WHERE nome = 'Nintendo 64';

INSERT IGNORE INTO jogos (nome, id_plataforma)
SELECT 'Dark Deception', id_plataforma FROM plataformas WHERE nome = 'PC';

INSERT IGNORE INTO jogos (nome, id_plataforma)
SELECT 'Minecraft', id_plataforma FROM plataformas WHERE nome = 'PC';

INSERT IGNORE INTO jogos (nome, id_plataforma)
SELECT 'Devil May Cry 3', id_plataforma FROM plataformas WHERE nome = 'PlayStation 2';

INSERT IGNORE INTO jogos (nome, id_plataforma)
SELECT 'Cuphead', id_plataforma FROM plataformas WHERE nome = 'PC';

-- =========================================================
-- DADOS INICIAIS: categorias
-- =========================================================
INSERT IGNORE INTO categorias (nome, descricao) VALUES
('Any%', 'Termine o jogo o mais rápido possível, usando os recursos permitidos pela categoria.'),
('100%', 'Complete todos os objetivos exigidos pela categoria.'),
('Glitchless', 'Termine o jogo sem utilizar glitches ou falhas conhecidas.');
