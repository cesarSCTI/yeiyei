    <!-- Bootstrap Bundle -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>

    <!-- JS base (helpers) -->
    <script src="<?= $yamsAsset('public/js/app.core.js') ?>"></script>

    <!-- JS del sistema (separado en 2 partes para poder modificar módulos sin afectar otros) -->
    <script src="<?= $yamsAsset('public/js/modules/app.part1.js') ?>"></script>
    <script src="<?= $yamsAsset('public/js/modules/app.part2.js') ?>"></script>

    <!-- Loader (se deja por compatibilidad de ruta) -->
    <script src="<?= $yamsAsset('public/js/app.js') ?>"></script>
</body>
</html>
