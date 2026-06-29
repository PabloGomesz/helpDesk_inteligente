<?php
// views/partials/header.php
// Abre o HTML, carrega Bootstrap (CDN) e o CSS do projeto.
// $titulo pode ser definido pela view antes de incluir este arquivo.

// Funções auxiliares para deixar as views mais limpas:

// Escapa texto para evitar quebra de layout / XSS.
function e($texto) {
    return htmlspecialchars($texto ?? '', ENT_QUOTES, 'UTF-8');
}

// Cor do "badge" de prioridade.
function corPrioridade($p) {
    return [
        'baixa'   => 'secondary',
        'media'   => 'info',
        'alta'    => 'warning',
        'critica' => 'danger',
    ][$p] ?? 'secondary';
}

// Cor e rótulo do "badge" de status.
function corStatus($s) {
    return [
        'aberto'       => 'primary',
        'em_andamento' => 'info',
        'aguardando'   => 'warning',
        'resolvido'    => 'success',
        'fechado'      => 'secondary',
    ][$s] ?? 'secondary';
}
function rotuloStatus($s) {
    return [
        'aberto'       => 'Aberto',
        'em_andamento' => 'Em andamento',
        'aguardando'   => 'Aguardando',
        'resolvido'    => 'Resolvido',
        'fechado'      => 'Fechado',
    ][$s] ?? $s;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($titulo) ? e($titulo) . ' | ' : '' ?>HelpDesk Inteligente</title>

    <!-- Bootstrap 5 + ícones (via CDN) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <!-- CSS próprio do projeto -->
    <link href="<?= ASSETS ?>css/style.css" rel="stylesheet">
</head>
<body class="bg-light">
