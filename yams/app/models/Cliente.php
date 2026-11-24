<?php
require_once "app/core/DB.php";

class Cliente {

    // Obtener todos los clientes
    public static function all() {
        $pdo = DB::pdo();

        $stmt = $pdo->query("
            SELECT 
                c.*,
                p.id AS pago_id,
                p.fecha_pago,
                p.proximo_pago,
                p.estado AS pago_estado_original,

                CASE
                    WHEN p.id IS NULL THEN 'Sin registro'
                    WHEN p.estado = 'Pagado' THEN 'Pagado'
                    WHEN DATEDIFF(DATE(p.proximo_pago), CURDATE()) < 0 THEN 'Vencido'
                    WHEN DATEDIFF(DATE(p.proximo_pago), CURDATE()) <= 5 THEN 'Por Vencer'
                    ELSE 'Pagado'
                END AS estado_pago,

                CASE
                    WHEN p.id IS NULL OR p.proximo_pago IS NULL THEN NULL
                    ELSE DATEDIFF(DATE(p.proximo_pago), CURDATE())
                END AS dias_restantes

            FROM clientes c
            LEFT JOIN pagos p 
                ON p.id = (
                    SELECT MAX(p2.id)
                    FROM pagos p2
                    WHERE p2.cliente_id = c.id
                )
            ORDER BY c.id DESC
        ");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Crear cliente nuevo
    public static function create($nombre, $telefono, $tipo_clase, $fecha_registro, $estado) {
        $pdo = DB::pdo();
        $stmt = $pdo->prepare("INSERT INTO clientes (nombre, telefono, tipo_clase, fecha_registro, estado)
                               VALUES (:n, :t, :tc, :fr, :e)");
        return $stmt->execute([
            "n"  => $nombre,
            "t"  => $telefono,
            "tc" => $tipo_clase,
            "fr" => $fecha_registro,
            "e"  => $estado
        ]);
    }

    // Buscar cliente por ID
    public static function find($id) {
        $pdo = DB::pdo();
        $stmt = $pdo->prepare("SELECT * FROM clientes WHERE id = :id LIMIT 1");
        $stmt->execute(["id" => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Actualizar cliente existente
    public static function update($id, $nombre, $telefono, $tipo_clase, $fecha_registro, $estado) {
        $pdo = DB::pdo();
        $stmt = $pdo->prepare("UPDATE clientes 
                               SET  nombre = :n,
                                    telefono = :t,
                                    tipo_clase = :tc,
                                    fecha_registro = :fr,
                                    estado = :e
                               WHERE id = :id");
        return $stmt->execute([
            "id" => $id,
            "n"  => $nombre,
            "t"  => $telefono,
            "tc" => $tipo_clase,
            "fr" => $fecha_registro,
            "e"  => $estado
        ]);
    }

    // Eliminar cliente
    public static function delete($id) {
        $pdo = DB::pdo();
        $stmt = $pdo->prepare("DELETE FROM clientes WHERE id = :id");
        return $stmt->execute(["id" => $id]);
    }

    // Guarda días y horas de asistencia del cliente
    public static function mdlGuardarHorariosCliente($idCliente, $horariosJson) {
        if (!$idCliente) return;

        $horarios = json_decode($horariosJson, true);
        if (!is_array($horarios)) return;

        $pdo = DB::pdo();

        // Borrar horarios anteriores
        $stmtDel = $pdo->prepare("DELETE FROM cliente_horarios WHERE cliente_id = :id");
        $stmtDel->execute(["id" => $idCliente]);

        // Insertar nuevos horarios
        $stmtIns = $pdo->prepare(
            "INSERT INTO cliente_horarios (cliente_id, dia, hora)
             VALUES (:cliente_id, :dia, :hora)"
        );

        foreach ($horarios as $h) {
            if (empty($h['dia']) || empty($h['hora'])) {
                continue;
            }

            $hora = $h['hora'];
            // Si viene como HH:MM, lo pasamos a HH:MM:SS
            if (strlen($hora) === 5) {
                $hora .= ':00';
            }

            $stmtIns->execute([
                "cliente_id" => $idCliente,
                "dia"        => $h['dia'],
                "hora"       => $hora,
            ]);
        }
    }


    // Obtiene los horarios de un cliente
    public static function mdlObtenerHorariosCliente($idCliente) {
        $pdo = DB::pdo();
        $stmt = $pdo->prepare(
            "SELECT dia, hora
             FROM cliente_horarios
             WHERE cliente_id = :id
             ORDER BY FIELD(dia, 'Lunes','Martes','Miércoles','Jueves','Viernes','Sábado')"
        );
        $stmt->execute(["id" => $idCliente]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}
