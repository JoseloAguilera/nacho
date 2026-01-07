<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistema de Gestión Comercial Nacho">
    <title><?= isset($title) ? $title . ' - ' : '' ?>Sistema Nacho</title>
    
    <!-- CSS -->
    <link rel="stylesheet" href="<?= base_url('assets/css/main.css') ?>">
    <?php if (isset($extraCSS)): ?>
        <?php foreach ($extraCSS as $css): ?>
            <link rel="stylesheet" href="<?= base_url($css) ?>">
        <?php endforeach; ?>
    <?php endif; ?>
</head>
<body>
