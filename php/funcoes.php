<?php
// Protege os textos mostrados na página contra código HTML indesejado.
function e($texto)
{
    return htmlspecialchars((string) $texto, ENT_QUOTES, 'UTF-8');
}

// Impede que uma pessoa envie uma run sem estar conectada.
function exigirLogin()
{
    if (!isset($_SESSION['id_runner'])) {
        header("Location: login.php");
        exit();
    }
}

// Transforma um tempo em segundos no formato HH:MM:SS.
function formatarTempo($segundos)
{
    $horas = floor($segundos / 3600);
    $minutos = floor(($segundos % 3600) / 60);
    $segundosRestantes = $segundos % 60;

    return sprintf("%02d:%02d:%02d", $horas, $minutos, $segundosRestantes);
}

// Recebe um tempo HH:MM:SS e transforma em segundos para o MySQL comparar.
// Retorna false quando o formato está errado.
function converterTempo($tempo)
{
    $partes = explode(':', trim($tempo));

    if (count($partes) !== 3) {
        return false;
    }

    // Confere se as três partes possuem somente números.
    foreach ($partes as $parte) {
        if ($parte === '' || !ctype_digit($parte)) {
            return false;
        }
    }

    $horas = (int) $partes[0];
    $minutos = (int) $partes[1];
    $segundos = (int) $partes[2];

    // Minutos e segundos não podem passar de 59.
    if ($minutos > 59 || $segundos > 59) {
        return false;
    }

    return ($horas * 3600) + ($minutos * 60) + $segundos;
}
?>
