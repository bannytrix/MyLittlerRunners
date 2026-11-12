<?php
require_once 'php/conexao.php';

// Apaga os dados de login da sessão.
$_SESSION = [];
session_destroy();

header("Location: index.php");
exit();
?>
