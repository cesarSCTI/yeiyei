<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yei Yei Academy</title>

    <!-- Bootstrap -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">

    <!-- FontAwesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <!-- Estilos propios (?v= fecha del archivo: la caché nunca sirve una versión vieja) -->
    <?php $yamsAsset = fn(string $ruta) => BASE_URL . $ruta . '?v=' . @filemtime(__DIR__ . '/../../../' . $ruta); ?>
    <link rel="stylesheet" href="<?= $yamsAsset('public/css/style.css') ?>">
</head>
<body class="wave-background">
<script>
  // Permisos por rol (ajustes 14 y 15): el JS no dibuja botones de editar/eliminar si es false
  window.YAMS_PUEDE_MODIFICAR = <?= yams_puede_modificar() ? 'true' : 'false' ?>;
</script>
