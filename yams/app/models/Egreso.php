<?php
require_once "app/core/DB.php";

class Egreso {

    // Obtener todos los egresos
    public static function all() {
        $pdo = DB::pdo();
        $stmt = $pdo->query("SELECT * FROM egresos ORDER BY fecha DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Crear un egreso
    public static function create($data) {
        $pdo = DB::pdo();
        $stmt = $pdo->prepare("
            INSERT INTO egresos (concepto, monto, fecha, forma_pago)
            VALUES (:concepto, :monto, :fecha, 'Efectivo')
        ");
        return $stmt->execute([
            'concepto' => $data['concepto'],
            'monto'    => $data['monto'],
            'fecha'    => $data['fecha'],
        ]);
    }

    // Actualizar un egreso
    public static function update($data) {
        $pdo = DB::pdo();
        $stmt = $pdo->prepare("
            UPDATE egresos 
            SET concepto = :concepto,
                monto    = :monto,
                fecha    = :fecha,
                forma_pago = 'Efectivo'
            WHERE id = :id
        ");
        return $stmt->execute([
            'concepto' => $data['concepto'],
            'monto'    => $data['monto'],
            'fecha'    => $data['fecha'],
            'id'       => $data['id'],
        ]);
    }

    // Buscar un egreso por id
    public static function find($id) {
        $pdo = DB::pdo();
        $stmt = $pdo->prepare("SELECT * FROM egresos WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Eliminar un egreso
    public static function delete($id) {
        $pdo = DB::pdo();
        $stmt = $pdo->prepare("DELETE FROM egresos WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    // Suma del mes (si la sigues usando en alguna parte)
    public static function sumThisMonth() {
        $pdo = DB::pdo();
        $stmt = $pdo->query("
            SELECT IFNULL(SUM(monto), 0) AS total
            FROM egresos
            WHERE MONTH(fecha) = MONTH(CURDATE())
              AND YEAR(fecha)  = YEAR(CURDATE())
        ");
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return (float)$row['total'];
    }
}
