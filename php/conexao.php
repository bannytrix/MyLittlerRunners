<?php
// Inicia a sessão para guardar os dados de quem está conectado.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Dados padrão do MySQL no XAMPP.
$servidor = "localhost";
$banco = "speedrun_db";
$usuario = "root";
$senha = "";

try {
    // PDO faz a conexão do PHP com o MySQL.
    $pdo = new PDO(
        "mysql:host=$servidor;dbname=$banco;charset=utf8mb4",
        $usuario,
        $senha
    );

    // Faz o PDO mostrar erros durante o desenvolvimento.
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Os resultados serão retornados como arrays associativos.
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $erro) {
    // Em caso de erro, confira se o MySQL está ligado e se o banco foi importado.
    die("Não foi possível conectar ao banco de dados. Confira o MySQL e o arquivo SQL.");
}
?>
