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

    <!-- Estilos propios -->
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body class="wave-background">

    <!-- Header -->
    <?php if (isset($header)) include $header; ?>

    <!-- Contenido principal -->
    <main class="container-fluid mt-4">
        <?php if (isset($view)) include $view; ?>
    </main>

    <!-- Footer -->
    <?php if (isset($footer)) include $footer; ?>

    <!-- Script principal -->
    <script src="<?= BASE_URL ?>public/js/app.js"></script>    
</body>
</html>