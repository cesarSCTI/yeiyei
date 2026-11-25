<?php
class DB {
    protected static $pdo;

    public static function pdo() {
        if (!self::$pdo) {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";

            self::$pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);

            // Alinea NOW()/CURDATE() de MariaDB con la zona horaria de PHP (index.php).
            // Se usa el desfase numérico (p. ej. -06:00) porque algunos hostings no
            // tienen cargadas las tablas de zonas horarias de MySQL.
            self::$pdo->exec("SET time_zone = '" . date('P') . "'");
        }

        return self::$pdo;
    }
}
