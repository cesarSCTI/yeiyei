<?php
require_once "app/core/DB.php";

class Pago {
    // Obtener todos los pagos con el nombre del cliente
    public static function all() {
        $pdo = DB::pdo();
        $stmt = $pdo->query("
            SELECT 
                p.id,
                p.cliente_id,
                p.monto,
                p.forma_pago,
                DATE(p.fecha_pago) AS fecha_pago,
                p.proximo_pago,
                p.estado,
                p.created_at,   
                c.nombre AS cliente,
                c.telefono  
            FROM pagos p
            LEFT JOIN clientes c ON p.cliente_id = c.id
            /*WHERE MONTH(p.fecha_pago) = MONTH(CURDATE())
            AND YEAR(p.fecha_pago)  = YEAR(CURDATE())*/
            ORDER BY p.id DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Crear un nuevo pago
    public static function create($data) {
        $pdo = DB::pdo();
        $stmt = $pdo->prepare("
            INSERT INTO pagos (cliente_id, monto, forma_pago, fecha_pago, proximo_pago, estado) 
            VALUES (:cid, :m, :fp, NOW(), :p, :e)
        ");
        return $stmt->execute([
            'cid' => $data['cliente_id'],
            'm'   => $data['monto'],
            "fp"  => $data["forma_pago"],
            'p'   => $data['proximo_pago'],
            'e'   => $data['estado']
        ]);
    }

    // Buscar pagos vencidos
    public static function countOverdue() {
        $pdo = DB::pdo();
        $stmt = $pdo->query("
            SELECT COUNT(*) as c 
            FROM pagos 
            WHERE proximo_pago < CURDATE() AND estado!='Pagado'
        ");
        return (int)$stmt->fetch()['c'];
    }

    // Sumar pagos del mes actual
    public static function sumThisMonth() {
        $pdo = DB::pdo();
        $stmt = $pdo->prepare("
            SELECT IFNULL(SUM(monto),0) as s 
            FROM pagos 
            WHERE MONTH(fecha_pago)=MONTH(CURDATE()) 
            AND YEAR(fecha_pago)=YEAR(CURDATE())
        ");
        $stmt->execute();
        return $stmt->fetch()['s'];
        //return (float)$stmt->fetch()['total'];
    }

    public static function find($id) {
        $pdo = DB::pdo();
        $stmt = $pdo->prepare("SELECT p.*, c.nombre AS cliente 
                            FROM pagos p
                            LEFT JOIN clientes c ON p.cliente_id = c.id
                            WHERE p.id = :id");
        $stmt->execute(["id" => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // =====================================================
    // ACTUALIZAR PAGO EXISTENTE
    // =====================================================
    public static function update($data) {
        $pdo = DB::pdo();

        // Si viene desde la actualización automática de estado
        if (isset($data["accion"]) && $data["accion"] === "autoUpdateEstado") {
            $stmt = $pdo->prepare("UPDATE pagos SET estado = :estado WHERE id = :id");
            return $stmt->execute([
                "estado" => $data["estado"],
                "id"     => $data["id"]
            ]);
        }

        // Si viene desde el modal Editar
        $stmt = $pdo->prepare("UPDATE pagos SET 
            cliente_id   = :cid,
            monto        = :m,
            forma_pago   = :fp,
            fecha_pago   = :f,
            proximo_pago = :p,
            estado       = :e
            WHERE id     = :id");

        return $stmt->execute([
            "cid" => $data["cliente_id"],
            "m"   => $data["monto"],
            "fp"  => $data["forma_pago"],
            "f"   => $data["fecha_pago"],
            "p"   => $data["proximo_pago"],
            "e"   => $data["estado"],
            "id"  => $data["id"]
        ]);
    }
}