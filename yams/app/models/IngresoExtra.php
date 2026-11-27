<?php
require_once "app/core/DB.php";

class IngresoExtra
{
    // Listar todos los ingresos extra
    public static function all()
    {
        $pdo = DB::pdo();
        $stmt = $pdo->query("
            SELECT id, evento, fecha, costo, forma_pago
            FROM ingresos_extras
            ORDER BY fecha DESC, id DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Suma de ingresos extra del mes en curso (TODOS los tipos)
    public static function sumThisMonth()
    {
        $pdo = DB::pdo();
        $stmt = $pdo->prepare("
            SELECT IFNULL(SUM(costo), 0) AS total
            FROM ingresos_extras
            WHERE MONTH(fecha) = MONTH(CURDATE())
              AND YEAR(fecha)  = YEAR(CURDATE())
        ");
        $stmt->execute();
        return (float)$stmt->fetch()['total'];
    }

    // Suma de ingresos extra del mes por tipo de pago
    public static function sumThisMonthByForma($forma)
    {
        $pdo = DB::pdo();
        $stmt = $pdo->prepare("
            SELECT IFNULL(SUM(costo), 0) AS total
            FROM ingresos_extras
            WHERE forma_pago = :fp
              AND MONTH(fecha) = MONTH(CURDATE())
              AND YEAR(fecha)  = YEAR(CURDATE())
        ");
        $stmt->execute(['fp' => $forma]);
        return (float)$stmt->fetch()['total'];
    }

    // Crear ingreso extra
    public static function create($data)
    {
        $pdo = DB::pdo();
        $stmt = $pdo->prepare("
            INSERT INTO ingresos_extras (evento, fecha, costo, forma_pago)
            VALUES (:evento, :fecha, :costo, :forma_pago)
        ");
        return $stmt->execute([
            'evento'     => $data['evento'],
            'fecha'      => $data['fecha'],
            'costo'      => $data['costo'],
            'forma_pago' => $data['forma_pago'] ?? 'Efectivo'
        ]);
    }

    // Buscar un ingreso extra por id
    public static function find($id)
    {
        $pdo = DB::pdo();
        $stmt = $pdo->prepare("
            SELECT id, evento, fecha, costo, forma_pago
            FROM ingresos_extras
            WHERE id = :id
        ");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Editar ingreso extra (ajuste 19)
    public static function update($data)
    {
        $pdo = DB::pdo();
        $stmt = $pdo->prepare("
            UPDATE ingresos_extras
            SET evento = :evento,
                fecha = :fecha,
                costo = :costo,
                forma_pago = :forma_pago
            WHERE id = :id
        ");
        return $stmt->execute([
            'evento'     => $data['evento'],
            'fecha'      => $data['fecha'],
            'costo'      => $data['costo'],
            'forma_pago' => $data['forma_pago'],
            'id'         => $data['id'],
        ]);
    }

    // Eliminar ingreso extra
    public static function delete($id)
    {
        $pdo = DB::pdo();
        $stmt = $pdo->prepare("DELETE FROM ingresos_extras WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}
